<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCmsTables extends Migration
{
    public function up()
    {
        // 1. Table `homepage_sections`
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'section_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'subtitle' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'layout_variant' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'default',
            ],
            'content_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('homepage_sections', true);

        // 2. Table `media_library`
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'original_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'file_size' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'caption' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('media_library', true);

        // 3. Seed Default Existing Homepage Sections
        $now = date('Y-m-d H:i:s');
        $defaultSections = [
            [
                'section_key'    => 'hero',
                'title'          => 'Temukan Hunian Impian Keluarga Modern & Asri',
                'subtitle'       => 'Kawasan hunian eksklusif dengan fasilitas lengkap, lingkungan hijau, keamanan 24 jam, dan akses cepat menuju jalan tol & stasiun utama.',
                'is_active'      => 1,
                'sort_order'     => 1,
                'layout_variant' => 'split', // split, full_image, centered, editorial, video_hero
                'content_json'   => json_encode([
                    'badge_text'         => 'Developer Properti Terpercaya & Legalitas Aman (SHM)',
                    'btn1_text'          => 'Jelajahi Tipe Rumah',
                    'btn1_link'          => 'properti',
                    'btn2_text'          => 'Hitung Simulasi KPR',
                    'btn2_link'          => 'kpr-calculator',
                    'stat1_value'        => '100%',
                    'stat1_label'        => 'Sertifikat SHM',
                    'stat2_value'        => 'DP 0%',
                    'stat2_label'        => 'Promo Cicilan KPR',
                    'stat3_value'        => '24/7',
                    'stat3_label'        => 'Keamanan & CCTV',
                    'media_type'         => 'image', // image or video
                    'image_url'          => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=900&auto=format&fit=crop&q=80',
                    'focal_x'            => 'center', // left, center, right
                    'focal_y'            => 'center', // top, center, bottom
                    'aspect_ratio'       => '4/3',    // auto, 16/9, 4/3, 1/1
                    'price_badge_prefix' => 'Harga Mulai Dari',
                    'price_badge_text'   => 'Rp 650 Juta-an',
                    'video_source'       => 'youtube', // youtube, vimeo, mp4
                    'video_url'          => '',
                    'video_poster'       => '',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key'    => 'properties',
                'title'          => 'Tipe Rumah & Klaster Unggulan',
                'subtitle'       => 'Pilihan Unit Terbaik',
                'is_active'      => 1,
                'sort_order'     => 2,
                'layout_variant' => '3_col', // 2_col, 3_col, 4_col, featured_grid, horizontal
                'content_json'   => json_encode([
                    'limit'       => 6,
                    'btn_text'    => 'Lihat Semua Tipe',
                    'btn_link'    => 'properti',
                    'show_filter' => 0,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key'    => 'about',
                'title'          => 'Membangun Harmoni dan Kualitas Hidup Lebih Baik',
                'subtitle'       => 'Tentang Pengembang & Visi Kawasan',
                'is_active'      => 1,
                'sort_order'     => 3,
                'layout_variant' => 'split', // split, centered, editorial
                'content_json'   => json_encode([
                    'description'  => 'Grand Harmoni Residence menghadirkan kawasan hunian berstandar tinggi yang menggabungkan keselarasan alam dengan infrastruktur modern, memberikan nilai investasi tinggi dan kenyamanan sejati bagi setiap keluarga.',
                    'highlight_1'  => 'Konstruksi bangunan terstandarisasi mutu SNI dengan pondasi kokoh.',
                    'highlight_2'  => 'Sistem kabel dan utilitas bawah tanah (underground utilities) rapi dan modern.',
                    'highlight_3'  => 'Aksesibilitas prima hanya beberapa menit ke fasilitas publik dan jalur transportasi utama.',
                    'image_url'    => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=900&auto=format&fit=crop&q=80',
                    'focal_x'      => 'center',
                    'focal_y'      => 'center',
                    'experience'   => '15+ Tahun',
                    'exp_label'    => 'Pengalaman Membangun Hunian Berkualitas',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key'    => 'features',
                'title'          => 'Kenyamanan & Nilai Investasi Terbaik',
                'subtitle'       => 'Mengapa Memilih Kami',
                'is_active'      => 1,
                'sort_order'     => 4,
                'layout_variant' => 'grid_4', // grid_4, grid_3, card_horizontal
                'content_json'   => json_encode([
                    'intro' => 'Dirancang dengan standar konstruksi tinggi dan tata ruang lingkungan terpadu.',
                    'items' => [
                        [
                            'icon'  => 'bi-shield-check',
                            'title' => 'Legalitas 100% Aman',
                            'desc'  => 'Sertifikat Hak Milik (SHM) dan IMB/PBG sudah pecah per unit, siap akad notaris.',
                        ],
                        [
                            'icon'  => 'bi-geo-alt-fill',
                            'title' => 'Lokasi Sangat Strategis',
                            'desc'  => 'Hanya 10 menit ke gerbang tol, pusat perbelanjaan, sekolah favorit, dan rumah sakit.',
                        ],
                        [
                            'icon'  => 'bi-tree-fill',
                            'title' => 'Eco Green Living',
                            'desc'  => 'Dilengkapi taman bermain tematik, jogging track, saluran underground, dan clubhouse eksklusif.',
                        ],
                        [
                            'icon'  => 'bi-cash-coin',
                            'title' => 'Kemudahan KPR',
                            'desc'  => 'Bekerjasama dengan lebih dari 10 bank BUMN & Swasta nasional dengan proses approval cepat.',
                        ],
                    ],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key'    => 'gallery',
                'title'          => 'Potret Kawasan & Fasilitas Hunian',
                'subtitle'       => 'Galeri Lingkungan & Desain Interior',
                'is_active'      => 1,
                'sort_order'     => 5,
                'layout_variant' => 'masonry', // grid, masonry, large_small, carousel
                'content_json'   => json_encode([
                    'items' => [
                        [
                            'image'   => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&auto=format&fit=crop&q=80',
                            'caption' => 'Fasad Rumah Modern Tropis',
                            'tag'     => 'Eksterior',
                        ],
                        [
                            'image'   => 'https://images.unsplash.com/photo-1600565193348-f74bd3c7ccdf?w=800&auto=format&fit=crop&q=80',
                            'caption' => 'Ruang Keluarga Lapang & Sejuk',
                            'tag'     => 'Interior',
                        ],
                        [
                            'image'   => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&auto=format&fit=crop&q=80',
                            'caption' => 'Dapur & Ruang Makan Minimalis',
                            'tag'     => 'Interior',
                        ],
                        [
                            'image'   => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&auto=format&fit=crop&q=80',
                            'caption' => 'Clubhouse & Kolam Renang Warga',
                            'tag'     => 'Fasilitas',
                        ],
                        [
                            'image'   => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800&auto=format&fit=crop&q=80',
                            'caption' => 'Taman Hijau & Jalur Jogging Asri',
                            'tag'     => 'Fasilitas',
                        ],
                        [
                            'image'   => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&auto=format&fit=crop&q=80',
                            'caption' => 'Balkon Atas Menatap Sunset',
                            'tag'     => 'Eksterior',
                        ],
                    ],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key'    => 'siteplan',
                'title'          => 'Pilih Posisi Kavling Impian Anda',
                'subtitle'       => 'Masterplan Interaktif & Ketersediaan Unit',
                'is_active'      => 1,
                'sort_order'     => 6,
                'layout_variant' => 'interactive', // interactive, preview_card
                'content_json'   => json_encode([
                    'btn_text'    => 'Buka Denah Masterplan Lengkap',
                    'btn_link'    => 'siteplan',
                    'description' => 'Klik pada nomor kavling untuk mengecek status ketersediaan unit, blok kavling, dan spesifikasi rumah secara interaktif.',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key'    => 'promo',
                'title'          => 'Promo Grand Launching & Subsidi Akad',
                'subtitle'       => 'Penawaran Terbatas Bulan Ini',
                'is_active'      => 1,
                'sort_order'     => 7,
                'layout_variant' => 'banner_card', // banner_card, split_image, countdown
                'content_json'   => json_encode([
                    'badge'       => 'KUOTA TERBATAS 10 UNIT PERTAMA',
                    'desc'        => 'Dapatkan Free DP 0%, Subsidi Biaya KPR up to 50 Juta, Bebas Biaya BPHTB & Notaris, serta bonus Smart Home System lengkap.',
                    'btn_text'    => 'Klaim Promo via WhatsApp',
                    'image_url'   => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&auto=format&fit=crop&q=60',
                    'focal_x'     => 'center',
                    'focal_y'     => 'center',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key'    => 'cta',
                'title'          => 'Siap Memiliki Rumah Impian Anda Hari Ini?',
                'subtitle'       => 'Konsultasikan Kebutuhan Hunian Anda',
                'is_active'      => 1,
                'sort_order'     => 8,
                'layout_variant' => 'center', // center, split_image, bg_image, bg_video
                'content_json'   => json_encode([
                    'desc'         => 'Konsultasikan kebutuhan hunian, simulasi KPR gratis, dan jadwalkan kunjungan show unit bersama konsultan properti kami.',
                    'btn1_text'    => 'Jadwalkan Survey Lokasi',
                    'btn1_type'    => 'whatsapp',
                    'btn2_text'    => 'Unduh E-Katalog PDF',
                    'btn2_link'    => 'brochure/download-global',
                    'bg_type'      => 'gradient', // gradient, image, video
                    'bg_image'     => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1600&auto=format&fit=crop&q=80',
                    'bg_video'     => '',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('homepage_sections')->insertBatch($defaultSections);
    }

    public function down()
    {
        $this->forge->dropTable('homepage_sections', true);
        $this->forge->dropTable('media_library', true);
    }
}
