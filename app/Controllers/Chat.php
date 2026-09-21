<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use CodeIgniter\HTTP\ResponseInterface;

class Chat extends BaseController
{
    /**
     * Handle incoming chat message from user with AI or smart rule-based fallback.
     */
    public function send(): ResponseInterface
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Metode HTTP tidak diizinkan.'
            ]);
        }

        $userMessage = trim((string) $this->request->getPost('message'));
        if (empty($userMessage)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Pesan tidak boleh kosong.'
            ]);
        }

        // Visitor's name if submitted via pre-chat lead form
        $userName = trim((string) $this->request->getPost('user_name'));

        // Conversation history array for multi-turn context memory
        $rawHistory = $this->request->getPost('history');
        $chatHistory = [];
        if (!empty($rawHistory)) {
            if (is_string($rawHistory)) {
                $decoded = json_decode($rawHistory, true);
                if (is_array($decoded)) {
                    $chatHistory = $decoded;
                }
            } elseif (is_array($rawHistory)) {
                $chatHistory = $rawHistory;
            }
        }

        // Fetch real-time properties from database for n8n metadata context
        $propertyModel = new PropertyModel();
        $properties = $propertyModel->getPropertiesWithThumbnail();

        $propertyListText = "";
        $minPrice = PHP_INT_MAX;
        $maxPrice = 0;
        foreach ($properties as $p) {
            $harga = (float) ($p['harga'] ?? 0);
            if ($harga > 0) {
                if ($harga < $minPrice) $minPrice = $harga;
                if ($harga > $maxPrice) $maxPrice = $harga;
            }
            $spesifikasi = !empty($p['spesifikasi_kamar']) ? $p['spesifikasi_kamar'] : '2 KT / 1 KM';
            $promoInfo = !empty($p['is_promo']) ? (" [SEDANG PROMO: " . ($p['promo_title'] ?: 'Promo Diskon') . " - " . ($p['promo_desc'] ?: '') . "]") : "";
            $propertyListText .= "- **" . $p['title'] . "** (" . $p['status'] . ")" . $promoInfo . " | Harga: Rp " . number_format($harga, 0, ',', '.') . " | LT: " . $p['luas_tanah'] . "m², LB: " . $p['luas_bangunan'] . "m² | Spek: " . $spesifikasi . " | Link: properti/" . ($p['slug'] ?? '') . "\n";
        }
        if ($minPrice === PHP_INT_MAX) $minPrice = 500000000;
        if ($maxPrice === 0) $maxPrice = 1500000000;

        // Company and branding variables
        $companyName    = $this->settings['company_name'] ?? 'Grand Harmoni Residence';
        $companyTagline = $this->settings['company_tagline'] ?? 'Hunian Modern, Asri & Nyaman';
        $companyAddress = $this->settings['company_address'] ?? 'Jakarta Barat';
        $companyPhone   = $this->settings['company_phone'] ?? '021-5558989';
        $companyWa      = $this->settings['company_whatsapp'] ?? '6281234567890';
        $vaName         = $this->settings['va_name'] ?? 'Sarah - Konsultan Properti';
        $vaPhone        = $this->settings['va_phone'] ?? $companyWa;
        $promoTitle     = $this->settings['promo_modal_title'] ?? 'Promo Spesial Bulan Ini';
        $promoDesc      = $this->settings['promo_modal_desc'] ?? 'Free BPHTB & Biaya Notaris, Subsidi Angsuran KPR, DP 0%';

        // Contextual WhatsApp link
        $waGreeting = !empty($userName) ? ('Halo ' . $companyName . ', saya ' . $userName . ', ingin konsultasi mengenai: ') : ('Halo ' . $companyName . ', saya ingin konsultasi mengenai: ');
        $directWaUrl = 'https://wa.me/' . $vaPhone . '?text=' . urlencode($waGreeting . $userMessage);

        // n8n Webhook Configuration
        $n8nWebhookUrl = trim((string) ($this->settings['n8n_webhook_url'] ?? 'https://ercy.app.n8n.cloud/webhook/a3da6735-771c-435f-b4c8-c9ae179329f0/chat'));

        // Execute n8n webhook
        if (!empty($n8nWebhookUrl)) {
            $n8nResponse = $this->callN8nWebhook(
                $n8nWebhookUrl,
                $userMessage,
                $userName,
                $chatHistory,
                [
                    'company_name'     => $companyName,
                    'company_tagline'  => $companyTagline,
                    'company_address'  => $companyAddress,
                    'company_phone'    => $companyPhone,
                    'company_whatsapp' => $companyWa,
                    'va_name'          => $vaName,
                    'va_phone'         => $vaPhone,
                    'promo_title'      => $promoTitle,
                    'promo_desc'       => $promoDesc,
                    'properties_text'  => $propertyListText,
                ]
            );

            if ($n8nResponse['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'reply'   => $n8nResponse['text'],
                    'source'  => 'n8n',
                    'wa_url'  => $directWaUrl,
                    'csrf'    => csrf_hash(),
                ]);
            } else {
                log_message('warning', 'n8n chat webhook failed: ' . ($n8nResponse['error'] ?? 'unknown'));
            }
        }

        // Fallback to built-in rule-based engine if n8n is offline or unreachable
        $fallbackReply = $this->getRuleBasedReply(
            $userMessage,
            $companyName,
            $companyAddress,
            $vaName,
            $vaPhone,
            $promoTitle,
            $promoDesc,
            $properties,
            $minPrice,
            $maxPrice,
            $userName
        );

        $hint = 'Peringatan: Webhook n8n belum merespon atau workflow n8n belum diaktifkan (Toggle Active di pojok kanan atas n8n).';

        return $this->response->setJSON([
            'success' => true,
            'reply'   => $fallbackReply['text'],
            'source'  => 'rule_based',
            'wa_url'  => $directWaUrl,
            'hint'    => $hint,
            'csrf'    => csrf_hash(),
        ]);
    }

    /**
     * Call n8n Chat Webhook with payload compatible with n8n Chat Trigger & Webhook Node
     */
    private function callN8nWebhook(
        string $webhookUrl,
        string $userMessage,
        string $userName = '',
        array $chatHistory = [],
        array $metadata = []
    ): array {
        // Prepare sessionId based on visitor IP + User Agent or session
        $sessionId = md5(($this->request->getIPAddress() ?? '127.0.0.1') . ($userName ?: 'guest'));

        // n8n Chat Trigger expects 'chatInput' or 'message'
        $payload = [
            'chatInput'   => $userMessage,
            'message'     => $userMessage,
            'sessionId'   => $sessionId,
            'user_name'   => $userName,
            'history'     => $chatHistory,
            'metadata'    => $metadata
        ];

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json, text/plain, */*'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode < 200 || $httpCode >= 300) {
            log_message('error', 'n8n Webhook Error: HTTP ' . $httpCode . ' - ' . ($response ?: $curlError));
            return ['success' => false, 'error' => 'HTTP ' . $httpCode . ': ' . ($curlError ?: $response)];
        }

        // Try decoding json response
        $replyText = '';
        $data = json_decode((string) $response, true);
        if (is_array($data)) {
            // Standard n8n Chat Trigger response formats:
            // {"output": "Halo, ..."} or {"text": "..."} or {"response": "..."} or [{"output": "..."}]
            if (isset($data['output'])) {
                $replyText = is_string($data['output']) ? $data['output'] : json_encode($data['output']);
            } elseif (isset($data['text'])) {
                $replyText = $data['text'];
            } elseif (isset($data['response'])) {
                $replyText = $data['response'];
            } elseif (isset($data['message'])) {
                $replyText = $data['message'];
            } elseif (isset($data['reply'])) {
                $replyText = $data['reply'];
            } elseif (isset($data[0]['output'])) {
                $replyText = $data[0]['output'];
            } elseif (isset($data[0]['text'])) {
                $replyText = $data[0]['text'];
            } else {
                // If it's another structured response, take the first string value
                foreach ($data as $v) {
                    if (is_string($v) && !empty(trim($v))) {
                        $replyText = $v;
                        break;
                    }
                }
            }
        } elseif (is_string($response) && !empty(trim($response))) {
            // Plain text returned
            $replyText = trim($response);
        }

        if (!empty($replyText)) {
            return ['success' => true, 'text' => trim($replyText)];
        }

        return ['success' => false, 'error' => 'Format respons dari n8n tidak berisi teks yang dikenali.'];
    }

    /**
     * Built-in intelligent 20+ real estate domain fallback engine with optimized priority matching
     */
    private function getRuleBasedReply(
        string $rawText,
        string $compName,
        string $compAddress,
        string $vaName,
        string $vaPhone,
        string $promoTitle,
        string $promoDesc,
        array $properties,
        float $minPrice,
        float $maxPrice,
        string $userName = ''
    ): array {
        $q = strtolower(trim($rawText));
        $sapaan = !empty($userName) ? "Kak {$userName}" : "Kak / Bapak / Ibu";

        // 1. GREETINGS & SMALL TALK
        if (preg_match('/\b(halo|hai|hay|hello|hi|pagi|siang|sore|malam|assalam|permisi|tes|test|ping)\b/i', $q) && strlen($q) < 35) {
            $greetings = [
                "Halo {$sapaan}! Selamat datang di **{$compName}**. Saya **{$vaName}**, konsultan properti resmi yang siap membantu Anda menemukan hunian idaman.\n\nAda yang bisa saya bantu hari ini? Anda bisa menanyakan katalog tipe rumah, simulasi cicilan KPR, promo bulan ini, maupun jadwal survey lokasi.",
                "Halo {$sapaan}! Senang sekali bisa menyapa Anda di **{$compName}** ({$compAddress}). 👋\n\nApakah Anda sedang mencari rumah untuk tempat tinggal keluarga atau untuk investasi? Silakan beri tahu kriteria yang diinginkan ya!",
                "Selamat datang {$sapaan}! Saya **{$vaName}** dari **{$compName}**. Kami siap membantu Anda mulai dari konsultasi tipe rumah, cek estimasi angsuran, hingga pendampingan survey ke lokasi.\n\nApa informasi utama yang ingin Anda ketahui terlebih dahulu?"
            ];
            return ['text' => $greetings[array_rand($greetings)]];
        }

        // 2. NAME / WHO ARE YOU
        if (preg_match('/\b(siapa (kamu|anda|namamu)|nama asisten|bot|robot|kamu manusia)\b/i', $q)) {
            return ['text' => "Saya **{$vaName}**, asisten virtual & konsultan properti resmi dari **{$compName}**. Saya siap memberikan informasi lengkap seputar hunian, harga, promo, dan simulasi KPR untuk Anda."];
        }

        // 3. SURVEY LOKASI & JANJI TEMU / DAFTAR JANJIAN (HIGH PRIORITY)
        if (preg_match('/\b(survey|kunjung|lihat unit|lihat rumah|show unit|janji temu|janjian|daftar janjian|bisa daftar|buat janji|booking survey|booking unit|booking|alamat kantor|jadwal survey|jadwal kunjungan)\b/i', $q)) {
            return ['text' => "Tentu saja bisa, {$sapaan}! Show Unit **{$compName}** buka **SETIAP HARI (Senin s.d. Minggu) pukul 09.00 - 17.00 WIB**.\n\n" .
                "Konsultan kami siap mendampingi kunjungan Anda untuk melihat langsung kualitas bangunan, tata ruang contoh, dan suasana asri kawasan perumahan.\n\n" .
                "📅 **Untuk mendaftar / konfirmasi janji temu survey:**\n" .
                "Silakan infokan rencana hari dan perkiraan jam kunjungan Anda, atau langsung klik tombol **WhatsApp** di bawah agar tim sales kami dapat menyiapkan unit contoh dan pendampingan terbaik untuk Anda!"];
        }

        // 4. BROSUR & PRICE LIST (HIGH PRIORITY)
        if (preg_match('/\b(brosur|brochure|pdf|pricelist|price list|katalog pdf|unduh brosur|download brosur|kirim brosur)\b/i', $q)) {
            return ['text' => "File **E-Brosur Lengkap & Price List Resmi (PDF)** berisi denah lantai, spesifikasi material, dan peta lokasi siap kami kirimkan langsung ke WhatsApp Anda.\n\n" .
                "Anda juga dapat mengunduh langsung di halaman detail masing-masing properti pada website ini."];
        }

        // 5. CASH KERAS & CASH BERTAHAP (HIGH PRIORITY)
        if (preg_match('/\b(cash bertahap|cash keras|hard cash|installment|cicil developer|tanpa bank|tunai bertahap)\b/i', $q)) {
            return ['text' => "Selain fasilitas KPR Bank, kami juga menyediakan skema pembayaran langsung ke developer:\n\n" .
                "1. **Cash Keras (Hard Cash):** Dapatkan potongan harga (diskon spesial pelunasan tunai) dan serah terima unit lebih cepat.\n" .
                "2. **Cash Bertahap (Installment Developer):** Cicilan bertahap langsung ke pengembang selama 6, 12, hingga 24 bulan tanpa proses BI Checking yang rumit.\n\n" .
                "Tertarik dengan simulasi perbandingan Cash vs KPR? Kami siap buatkan rinciannya untuk Anda."];
        }

        // 6. BI CHECKING / SLIK OJK / PAYLATER (HIGH PRIORITY)
        if (preg_match('/\b(bi checking|slik|ojk|paylater|pinjol|kartu kredit|kolektibilitas|kol 1|kol 2|kol 3|skor kredit|blacklist)\b/i', $q)) {
            return ['text' => "Pemeriksaan riwayat kredit (**SLIK OJK / BI Checking**) adalah salah satu faktor penentu persetujuan KPR oleh perbankan.\n\n" .
                "🔑 **Tips Penting Lolos BI Checking:**\n" .
                "• Pastikan status kredit Anda berada di **Kolektibilitas 1 (Lancar)** tanpa ada tunggakan berjalan.\n" .
                "• Jika memiliki tagihan **Paylater, Pinjol, atau Kartu Kredit**, sangat disarankan untuk **melunasinya terlebih dahulu** sebelum pengajuan berkas KPR agar rasio utang bersih.\n" .
                "• Minta surat keterangan lunas jika baru saja melunasi pinjaman sebelumnya.\n\n" .
                "Ingin kami bantu konsultasikan profil SLIK Anda sebelum berkas masuk ke bank? Tim sales kami siap membantu memberikan solusi terbaik."];
        }

        // 7. SYARAT & BERKAS DOKUMEN KPR (HIGH PRIORITY)
        if (preg_match('/\b(syarat kpr|berkas kpr|dokumen kpr|persyaratan kpr|syarat pengajuan|pengajuan kpr|ajukan kpr|cara mengajukan)\b/i', $q)) {
            return ['text' => "Berikut persyaratan dokumen untuk pengajuan KPR di **{$compName}**:\n\n" .
                "📋 **Untuk Karyawan / Pegawai:**\n" .
                "1. E-KTP (Pemohon & Pasangan jika sudah menikah)\n" .
                "2. Kartu Keluarga (KK) & NPWP Pribadi\n" .
                "3. Surat Nikah / Surat Cerai (jika relevan)\n" .
                "4. Slip Gaji 3 bulan terakhir asli\n" .
                "5. Rekening Koran payroll 3 bulan terakhir\n" .
                "6. Surat Keterangan Kerja aktif (SK Pegawai)\n\n" .
                "📋 **Untuk Wiraswasta / Profesional:**\n" .
                "1. KTP, KK, NPWP Pribadi & Usaha\n" .
                "2. Rekening Koran operasional 6 bulan terakhir\n" .
                "3. NIB / SIUP / SKU / Izin Praktek & Catatan Keuangan Usaha\n\n" .
                "Seluruh berkas akan dibantu dan didampingi penuh oleh tim administrasi in-house kami sampai SP3K dari bank terbit!"];
        }

        // 8. GAJI & KEMAMPUAN ANGSURAN (DEBT SERVICE RATIO / DSR)
        if (preg_match('/\b(gaji|penghasilan|pendapatan|salary|take home pay|kemampuan bayar|syarat gaji|joint income)\b/i', $q)) {
            return ['text' => "Untuk pengajuan KPR, perbankan umumnya mensyaratkan **cicilan bulanan maksimal 30% - 40% dari total penghasilan bulanan** (*Debt Service Ratio*).\n\n" .
                "📌 **Contoh Perhitungan Kelayakan Gaji:**\n" .
                "• Gaji Rp 6.000.000 / bulan ➜ Plafon cicilan aman: ~Rp 2.000.000 - Rp 2.400.000/bln (Tenor 20-25 thn)\n" .
                "• Gaji Rp 10.000.000 / bulan ➜ Plafon cicilan aman: ~Rp 3.500.000 - Rp 4.000.000/bln (Tenor 15-20 thn)\n" .
                "• Gaji Rp 15.000.000 / bulan ➜ Plafon cicilan aman: ~Rp 5.000.000 - Rp 6.000.000/bln\n\n" .
                "💡 **Tips:** Bagi pasangan suami-istri yang keduanya bekerja, Anda bisa mengajukan skema **Joint Income** agar plafon KPR yang disetujui bank menjadi jauh lebih tinggi! Tim kami siap membantu proses analisa pra-KPR gratis."
            ];
        }

        // 9. BIAYA TAMBAHAN TRANSAKSI (BPHTB, NOTARIS, AJB, DLL)
        if (preg_match('/\b(bphtb|biaya notaris|biaya kpr|biaya ajb|biaya bbn|biaya lain|biaya tambahan|biaya all in|all in|all-in)\b/i', $q)) {
            return ['text' => "Dalam pembelian rumah, umumnya terdapat beberapa pos biaya:\n\n" .
                "1. **BPHTB (Pajak Pembeli):** 5% x (Harga Rumah - NPOPTKP)\n" .
                "2. **Biaya Notaris & PPAT:** Pembuatan AJB (Akta Jual Beli) & BBN (Bea Balik Nama)\n" .
                "3. **Biaya Proses KPR:** Provisi Bank, Administrasi, Asuransi Jiwa & Kebakaran, APHT\n\n" .
                "🎉 **KABAR GEMBIRA:** Pada program promo **{$promoTitle}** di {$compName}, biaya **BPHTB, AJB, Biaya Notaris, dan Subsidi KPR sudah GRATIS (ALL-IN)**! Anda cukup menyiapkan booking fee dan DP saja."];
        }

        // 10. LEGALITAS & SERTIFIKAT (SHM, IMB/PBG, NOTARIS)
        if (preg_match('/\b(legalitas|sertifikat|shm|hgb|imb|pbg|amdal|notaris|ppat|keamanan investasi|izin mendirikan)\b/i', $q)) {
            return ['text' => "Legalitas di **{$compName}** terjamin 100% aman dan jelas:\n\n" .
                "✅ **Sertifikat Hak Milik (SHM):** Sudah split/pecah per kavling (status kepemilikan tertinggi dan teraman di Indonesia).\n" .
                "✅ **Persetujuan Bangunan Gedung (PBG/IMB):** Lengkap dan resmi dari dinas perizinan daerah.\n" .
                "✅ **Siteplan & AMDAL:** Telah disahkan secara resmi oleh Pemda.\n" .
                "✅ **Transaksi Resmi:** Penandatanganan AJB / PPJB dilakukan di hadapan Notaris / PPAT rekanan resmi terdaftar.\n\n" .
                "Anda dapat memeriksa langsung copy legalitas saat berkunjung ke Marketing Gallery kami."];
        }

        // 11. BEBAS BANJIR & DRAINASE (HIGH PRIORITY)
        if (preg_match('/\b(banjir|bebas banjir|drainase|resapan|elevasi tanah|saluran air)\b/i', $q)) {
            return ['text' => "Kawasan **{$compName}** dirancang dengan perencanaan sipil modern dan **100% BEBAS BANJIR**:\n\n" .
                "🛡️ **Sistem Perlindungan Banjir Kami:**\n" .
                "1. **Elevasi Kawasan Tinggi:** Level tanah perumahan dinaikkan secara matang di atas rata-rata elevasi jalan raya sekitar.\n" .
                "2. **Drainase Tertutup (Underground Drainage):** Aliran air hujan dialirkan lancar melalui gorong-gorong tertutup sehingga lingkungan tetap rapi, higienis, dan tidak berbau.\n" .
                "3. **Kolam Retensi & Sumur Resapan:** Disediakan resapan air mandiri di dalam masterplan kawasan perumahan."];
        }

        // 12. SPESIFIKASI BANGUNAN & MATERIAL KONSTRUKSI
        if (preg_match('/\b(spesifikasi|spek|pondasi|bata merah|hebel|atap|genteng|baja ringan|granit|keramik|sanitair|toto|listrik|pln|pdam|sumur bor)\b/i', $q)) {
            return ['text' => "🏗️ **Spesifikasi Teknis & Material Premium di {$compName}:**\n\n" .
                "• **Pondasi:** Batu Kali & Struktur Beton Bertulang (Cakar Ayam kokoh)\n" .
                "• **Dinding:** Bata Merah / Hebel Berkualitas, Plester Aci & Cat Weatherproof\n" .
                "• **Lantai:** Homogeneous Granite Tile 60x60 cm (Mewah & Sejuk)\n" .
                "• **Rangka Atap:** Baja Ringan Zinc/Galvalum & Genteng Flat Beton\n" .
                "• **Plafon:** Gypsum Board dengan ketinggian *High Ceiling* 3.6 - 4.0 Meter\n" .
                "• **Kusen & Pintu:** Kusen Aluminium Powder Coating & Smart Door Lock\n" .
                "• **Sanitair:** Kloset Duduk Toto / American Standard & Shower set\n" .
                "• **Utilitas:** Listrik PLN 1300/2200 VA & Air Bersih PDAM / Sumur Bor jernih"];
        }

        // 13. FASILITAS KAWASAN / PERUMAHAN
        if (preg_match('/\b(fasilitas|security|keamanan|satpam|cctv|one gate|taman|playground|musholla|masjid|jogging track|row jalan)\b/i', $q)) {
            return ['text' => "🏡 **Fasilitas Lengkap di Lingkungan {$compName}:**\n\n" .
                "• **One Gate System:** Akses keluar-masuk tunggal dengan kartu akses / RFID\n" .
                "• **Keamanan 24 Jam & CCTV:** Penjagaan pos security dan pengawasan kamera 24/7\n" .
                "• **Children Playground & Taman Tematik:** Area bermain ramah anak dan ruang hijau asri\n" .
                "• **Sarana Ibadah:** Musholla / Masjid di dalam kawasan\n" .
                "• **Jogging Track:** Area olahraga santai bersama keluarga di pagi/sore hari\n" .
                "• **Row Jalan Lebar:** Lebar jalan 6 s/d 10 meter, leluasa untuk papasan 2 mobil"];
        }

        // 14. PROMO / DISKON / CASHBACK / DP 0%
        if (preg_match('/\b(promo|diskon|cashback|dp 0|subsidi|potongan harga|hadiah|bonus)\b/i', $q)) {
            $promoUnitsText = "";
            $promoUnits = array_filter($properties, fn($p) => !empty($p['is_promo']));
            if (!empty($promoUnits)) {
                $promoUnitsText = "\n\n🔥 **Tipe Unit dengan Promo Khusus Saat Ini:**\n";
                foreach ($promoUnits as $pu) {
                    $promoUnitsText .= "• **" . $pu['title'] . "**: " . ($pu['promo_title'] ?: 'Promo Spesial') . " (" . ($pu['promo_desc'] ?: 'Diskon & Subsidi Khusus') . ")\n";
                }
            }

            return ['text' => "🎁 **PROGRAM PROMO RESMI BULAN INI: {$promoTitle}**\n\n" .
                "Keuntungan spesial yang bisa Anda nikmati saat ini:\n" .
                "• **{$promoDesc}**\n" .
                "• Opsi **DP 0% / DP Ringan** dapat dicicil\n" .
                "• Free Biaya BPHTB & Biaya Pengurusan Notaris/PPAT\n" .
                "• Subsidi Suku Bunga KPR & Subsidi Biaya Akad Bank\n" .
                "• Bonus Tambahan: Free Smart Door Lock / Kanopi (kuota terbatas)" .
                $promoUnitsText . "\n" .
                "⚠️ *Promo ini berlaku terbatas untuk pemesanan unit di bulan ini. Anda juga bisa mengklik tombol 'Cek Promo Unit' pada katalog untuk melihat pop-up rinciannya!*"];
        }

        // 15. LOKASI, ALAMAT & AKSES STRATEGIS
        if (preg_match('/\b(lokasi|alamat|posisi|dimana|daerah|peta|akses|jalan tol|stasiun|rumah sakit|sekolah|mall)\b/i', $q)) {
            return ['text' => "📍 **Lokasi & Aksesibilitas {$compName}:**\n\n" .
                "Perumahan kami beralamat di: **{$compAddress}**.\n\n" .
                "🚗 **Aksesibilitas & Fasilitas Sekitar:**\n" .
                "• 5 - 10 Menit ke Pintu Gerbang Tol Utama\n" .
                "• Dekat dengan Stasiun KRL / Transportasi Umum\n" .
                "• Dekat dengan Pusat Perbelanjaan / Mall & Pasar Modern\n" .
                "• Dikelilingi Sekolah Favorit, Universitas, dan Rumah Sakit Umum\n" .
                "• Kawasan berkembang pesat dengan potensi kenaikan nilai investasi yang sangat tinggi.\n\n" .
                "Konsultan kami siap membagikan titik lokasi Google Maps resmi melalui WhatsApp."];
        }

        // 16. ALUR / PROSES PEMBELIAN RUMAH
        if (preg_match('/\b(alur|tahapan|tahap|proses beli|langkah beli|cara beli|bagaimana cara|prosedur beli)\b/i', $q)) {
            return ['text' => "📌 **Tahapan Mudah Membeli Rumah di {$compName}:**\n\n" .
                "1. **Survey Lokasi:** Melihat unit contoh & memilih nomor kavling idaman.\n" .
                "2. **Booking Fee:** Mengunci harga promo & kavling yang dipilih.\n" .
                "3. **Pemberkasan:** Mengumpulkan dokumen (KTP, KK, Slip Gaji, dll), dibantu penuh oleh tim in-house kami.\n" .
                "4. **Proses Bank (KPR):** Analisa data hingga Surat Persetujuan Kredit (SP3K) terbit.\n" .
                "5. **Akad Kredit / Notaris:** Penandatanganan akad di hadapan Notaris & Bank.\n" .
                "6. **Serah Terima Kunci (STK):** Rumah siap dihuni lengkap dengan sertifikat garansi pemeliharaan."];
        }

        // 17. INVESTASI PROPERTI & CAPITAL GAIN
        if (preg_match('/\b(investasi|sewa|capital gain|untung|nilai properti|prospek investasi)\b/i', $q)) {
            return ['text' => "Membeli unit di **{$compName}** merupakan instrumen investasi properti yang sangat prospektif:\n\n" .
                "📈 **Kenaikan Nilai (Capital Gain):** Diproyeksikan naik 10% - 18% per tahun seiring pesatnya pembangunan infrastruktur jalan tol dan fasilitas publik sekitar.\n" .
                "💰 **Potensi Passive Income:** Tingginya permintaan sewa hunian keluarga di area ini memberikan *rental yield* yang menarik (~6% - 8% per tahun).\n\n" .
                "Membeli di tahap awal (*early bird phase*) adalah momentum terbaik untuk mengamankan harga terendah!"];
        }

        // 18. SITEPLAN / DENAH KAVLING / MASTER PLAN / POSISI KAVLING
        if (preg_match('/\b(siteplan|site plan|denah|kavling|master plan|peta perumahan|blok|tata letak|posisi rumah|posisi kavling|hook)\b/i', $q)) {
            return ['text' => "🗺️ **Master Siteplan & Peta Kavling Interaktif {$compName}:**\n\n" .
                "Kami menyediakan fitur **Siteplan Interaktif** di mana {$sapaan} dapat mengeksplorasi posisi setiap nomor kavling, melihat tipe rumah, letak sudut/hook, dekat taman, dan mengecek status ketersediaan unit secara real-time!\n\n" .
                "📍 **Status Kavling di Peta:**\n" .
                "• 🟢 **Hijau:** Unit Masih Tersedia (Ready)\n" .
                "• 🟡 **Kuning:** Sedang Dalam Proses Booking\n" .
                "• 🔴 **Merah:** Terjual (Sold Out)\n\n" .
                "👉 Anda dapat langsung membuka menu **Siteplan** di atas atau berkonsultasi via WhatsApp untuk memilih kavling terbaik!"];
        }

        // 18. KONTAK WHATSAPP & TELEPON
        if (preg_match('/\b(kontak|hubungi|whatsapp|nomor wa|telepon|hp|sales|marketing)\b/i', $q)) {
            return ['text' => "Anda dapat menghubungi tim marketing resmi **{$compName}** melalui:\n\n" .
                "📱 **WhatsApp Marketing:** {$vaPhone}\n" .
                "☎️ **Telepon Kantor:** {$companyPhone}\n" .
                "🏢 **Alamat:** {$compAddress}\n\n" .
                "Konsultan kami siap melayani konsultasi online maupun membuat janji temu di Marketing Gallery."];
        }

        // 19. BUDGET / HARGA SPESIFIK DENGAN NOMINAL
        if (preg_match('/(budget|dana|uang|harga)\s*(?:sekitar|dibawah|kurang dari|maksimal|max|di bawah)?\s*(\d+)\s*(juta|jt|milyar|miliar|m|jt-an)?/i', $q, $matches)) {
            $number = (float) $matches[2];
            $unit   = strtolower($matches[3] ?? '');
            $targetPrice = $number;
            if (str_contains($unit, 'm')) {
                $targetPrice = $number * 1000000000;
            } elseif (str_contains($unit, 'j') || empty($unit)) {
                if ($number < 100) {
                    $targetPrice = $number * 1000000;
                } else {
                    $targetPrice = $number * 1000000;
                }
            }

            $matchedProps = [];
            foreach ($properties as $p) {
                if ((float)$p['harga'] <= ($targetPrice * 1.15)) {
                    $matchedProps[] = $p;
                }
            }

            if (!empty($matchedProps)) {
                $text = "Berdasarkan budget sekitar **Rp " . number_format($targetPrice, 0, ',', '.') . "**, berikut rekomendasi unit yang sangat cocok untuk Anda di **{$compName}**:\n\n";
                foreach (array_slice($matchedProps, 0, 3) as $p) {
                    $text .= "• **" . $p['title'] . "** - Rp " . number_format($p['harga'], 0, ',', '.') . " (" . ($p['spesifikasi_kamar'] ?: '2 KT / 1 KM') . ", LT: {$p['luas_tanah']}m², LB: {$p['luas_bangunan']}m²)\n";
                }
                $text .= "\nSemua unit sudah bersertifikat **SHM** dan siap diajukan KPR dengan promo subsidi biaya notaris & DP ringan. Mau kami buatkan simulasi cicilannya?";
                return ['text' => $text];
            }
        }

        // 20. KAMAR TIDUR / SPESIFIKASI RUANGAN (2 KT, 3 KT, 2 LANTAI)
        if (preg_match('/\b(2 kamar|3 kamar|4 kamar|kamar tidur|toilet|kamar mandi|2 lantai|1 lantai|carport)\b/i', $q)) {
            $text = "Kami memiliki varian tipe hunian dengan tata ruang fungsional & modern:\n\n";
            $found = 0;
            foreach ($properties as $p) {
                if (stripos($p['spesifikasi_kamar'] ?? '', 'kamar') !== false || stripos($p['title'], 'tipe') !== false) {
                    $text .= "• **" . $p['title'] . "** (" . ($p['spesifikasi_kamar'] ?: '2 KT / 1 KM') . ") - Rp " . number_format($p['harga'], 0, ',', '.') . "\n";
                    $found++;
                    if ($found >= 3) break;
                }
            }
            $text .= "\nSetiap unit sudah dilengkapi **Carport Mobil**, ruang tamu lapang dengan plafon tinggi (*high ceiling*), dapur, dan sisa tanah belakang untuk taman/ventilasi.";
            return ['text' => $text];
        }

        // 21. SIMULASI KPR & ESTIMASI CICILAN
        if (preg_match('/\b(kpr|cicil|angsur|simulasi|bunga|tenor|kalkulator|bank)\b/i', $q)) {
            $estPrice = $minPrice > 0 ? $minPrice : 600000000;
            $dp = $estPrice * 0.05; // 5% DP
            $loan = $estPrice - $dp;
            $rate = 0.06; // 6% annual fix promo

            $calcPmt = function($principal, $annualRate, $years) {
                $monthlyRate = $annualRate / 12;
                $months = $years * 12;
                return ($principal * $monthlyRate * pow(1 + $monthlyRate, $months)) / (pow(1 + $monthlyRate, $months) - 1);
            };

            $c10 = $calcPmt($loan, $rate, 10);
            $c15 = $calcPmt($loan, $rate, 15);
            $c20 = $calcPmt($loan, $rate, 20);
            $c25 = $calcPmt($loan, $rate, 25);

            $text = "Kami bekerjasama dengan bank BUMN (BTN, Mandiri, BRI, BNI) dan swasta nasional dengan **bunga promo fix mulai 5.5% - 6.5%**.\n\n";
            $text .= "📊 **Estimasi Simulasi Cicilan KPR (Harga Unit Rp " . number_format($estPrice, 0, ',', '.') . ", DP 5%):**\n";
            $text .= "• Tenor 10 Tahun : ~Rp " . number_format($c10, 0, ',', '.') . " / bulan\n";
            $text .= "• Tenor 15 Tahun : ~Rp " . number_format($c15, 0, ',', '.') . " / bulan\n";
            $text .= "• Tenor 20 Tahun : ~Rp " . number_format($c20, 0, ',', '.') . " / bulan\n";
            $text .= "• Tenor 25 Tahun : ~Rp " . number_format($c25, 0, ',', '.') . " / bulan\n\n";
            $text .= "💡 *Tersedia juga opsi **DP 0%** dan subsidi biaya KPR pada promo bulan ini.* Anda juga bisa mencoba menu **Kalkulator KPR** di website kami.";
            return ['text' => $text];
        }

        // 22. KATALOG / TIPE / HARGA RUMAH (GENERIC)
        if (preg_match('/\b(harga|tipe|type|katalog|unit|pilihan rumah|daftar rumah|daftar harga|pilihan unit|rumah)\b/i', $q)) {
            $text = "Di **{$compName}**, kami menyediakan beberapa pilihan tipe hunian modern & asri untuk kenyamanan keluarga Anda:\n\n";
            foreach (array_slice($properties, 0, 4) as $p) {
                $text .= "🏡 **" . $p['title'] . "**\n";
                $text .= "  - Harga: **Rp " . number_format($p['harga'], 0, ',', '.') . "**\n";
                $text .= "  - Luas: LT " . $p['luas_tanah'] . "m² / LB " . $p['luas_bangunan'] . "m²\n";
                $text .= "  - Spesifikasi: " . ($p['spesifikasi_kamar'] ?: '2 Kamar Tidur, 1 Kamar Mandi') . "\n";
                $text .= "  - Status: *" . $p['status'] . "*\n\n";
            }
            $text .= "✨ **Keuntungan Ekstra:** Semua unit sudah bersertifikat **SHM**, berdesain modern minimalis dengan sirkulasi udara sehat, dan bergaransi retensi bangunan.\n\nApakah ada tipe tertentu yang paling menarik perhatian Anda?";
            return ['text' => $text];
        }

        // 23. DEFAULT CONTEXTUAL HUMAN-LIKE ADVISOR FALLBACK
        $defaultProps = array_slice($properties, 0, 3);
        $propSnippets = "";
        foreach ($defaultProps as $p) {
            $propSnippets .= "• **" . $p['title'] . "** - Rp " . number_format($p['harga'], 0, ',', '.') . " (" . ($p['spesifikasi_kamar'] ?: '2 KT / 1 KM') . ")\n";
        }

        $fallbackResponses = [
            "Terima kasih atas pertanyaannya, {$sapaan}! Di **{$compName}**, kami berkomitmen menghadirkan hunian modern, asri, dan aman bersertifikat SHM.\n\nBeberapa pilihan unit terpopuler kami saat ini:\n{$propSnippets}\nPromo bulan ini: **{$promoTitle}** ({$promoDesc}).\n\nUntuk informasi lebih detail mengenai simulasi cicilan, spesifikasi unit, atau jadwal survey lokasi, Anda dapat langsung mengetik pertanyaan di sini atau terhubung via WhatsApp marketing kami.",
            "Halo {$sapaan}! Saya **{$vaName}** dari **{$compName}**. Mengenai kebutuhan hunian Anda, kami memiliki berbagai pilihan tipe rumah mulai dari harga **Rp " . number_format($minPrice, 0, ',', '.') . "** dengan fasilitas One Gate System, keamanan 24 jam, dan bebas banjir.\n\nApakah Anda ingin kami kirimkan e-brosur lengkap, simulasi angsuran KPR, atau mengatur jadwal kunjungan ke show unit kami?",
        ];

        return ['text' => $fallbackResponses[array_rand($fallbackResponses)]];
    }
}
