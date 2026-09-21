<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Action Toolbar (Hidden during Print) -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 p-3 bg-white rounded-4 shadow-sm border d-print-none">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                        <i class="bi bi-file-earmark-check-fill fs-5"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Lembar E-Brosur & Spesifikasi Digital Resmi</h6>
                        <small class="text-muted">Data Anda telah tercatat. Anda dapat langsung mencetak atau menyimpan lembar ini sebagai PDF.</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" onclick="window.print()">
                        <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan sebagai PDF
                    </button>
                    <?php if (!empty($property)): ?>
                        <a href="<?= site_url('properti/' . $property['slug']) ?>" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Detail Web
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('properti') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-houses me-1"></i> Katalog Properti
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Printable E-Brochure Sheet Card -->
            <div class="card border-0 shadow rounded-4 overflow-hidden bg-white p-4 p-md-5 print-container" id="printableBrochure">
                <!-- Header Developer -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-4 mb-4 gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <?php 
                            $companyLogo = $settings['company_logo'] ?? '';
                            if (!empty($companyLogo) && is_file(FCPATH . 'uploads/settings/' . $companyLogo)): 
                            ?>
                                <img src="<?= base_url('uploads/settings/' . $companyLogo) ?>" alt="Logo" style="height: 38px; width: auto; object-fit: contain;">
                            <?php else: ?>
                                <i class="bi bi-buildings-fill text-primary fs-3"></i>
                            <?php endif; ?>
                            <h4 class="fw-bold text-dark mb-0"><?= esc($settings['company_name'] ?? 'Grand Harmoni Residence') ?></h4>
                        </div>
                        <small class="text-muted d-block"><?= esc($settings['company_tagline'] ?? 'Hunian Mewah, Asri, dan Strategis') ?></small>
                        <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i> <?= esc($settings['company_address'] ?? 'Boulevard Raya No. 88') ?></small>
                    </div>
                    <div class="text-sm-end">
                        <span class="badge bg-primary px-3 py-2 rounded-pill fs-6 mb-1">OFFICIAL SPEC-SHEET</span>
                        <small class="text-muted d-block">Marketing: <?= esc($settings['company_phone'] ?? '021-5558989') ?></small>
                        <small class="text-muted d-block">WhatsApp: <?= esc($settings['company_whatsapp'] ?? '6281234567890') ?></small>
                    </div>
                </div>

                <?php if (!empty($property)): ?>
                    <!-- Property Details -->
                    <div class="row g-4 mb-4 align-items-center">
                        <div class="col-md-6">
                            <?php 
                            $mainImg = 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&auto=format&fit=crop&q=80';
                            if (!empty($property['images'][0]['image_name'])) {
                                $first = $property['images'][0]['image_name'];
                                $mainImg = str_starts_with($first, 'http') ? $first : base_url('uploads/properties/' . $first);
                            }
                            ?>
                            <img src="<?= esc($mainImg) ?>" alt="<?= esc($property['title']) ?>" class="w-100 rounded-4 shadow-sm object-fit-cover" style="height: 280px;">
                        </div>
                        <div class="col-md-6">
                            <span class="badge bg-success rounded-pill px-3 py-1 mb-2">Tipe Rumah Pilihan</span>
                            <h2 class="fw-bold text-dark mb-1"><?= esc($property['title']) ?></h2>
                            <small class="text-muted d-block mb-3">Legalitas: <strong>SHM (Sertifikat Hak Milik) + PBG</strong></small>

                            <div class="p-3 bg-light rounded-3 mb-3">
                                <small class="text-muted d-block">Harga Penawaran Resmi</small>
                                <h3 class="fw-extrabold text-primary mb-0">Rp <?= number_format($property['harga'], 0, ',', '.') ?></h3>
                            </div>

                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <div class="p-2 border rounded-3 bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Luas Tanah</small>
                                        <strong class="text-dark"><?= esc($property['luas_tanah']) ?> m²</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded-3 bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Luas Bangunan</small>
                                        <strong class="text-dark"><?= esc($property['luas_bangunan']) ?> m²</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded-3 bg-white">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Kamar</small>
                                        <strong class="text-dark" style="font-size: 0.85rem;"><?= esc($property['spesifikasi_kamar'] ?: '2 KT/1 KM') ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($property['is_promo'])): ?>
                        <div class="alert alert-warning border-0 rounded-3 p-3 mb-4">
                            <h6 class="fw-bold mb-1"><i class="bi bi-fire text-danger me-1"></i> <?= esc($property['promo_title'] ?: 'Promo Spesial Unit') ?></h6>
                            <p class="mb-0 small"><?= nl2br(esc($property['promo_desc'] ?: 'Subsidi DP, Free Biaya Notaris & Diskon Khusus Booking Bulan Ini.')) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="bi bi-card-text text-primary me-2"></i>Deskripsi & Spesifikasi Material</h6>
                        <p class="text-secondary small lh-base mb-0">
                            <?= nl2br(esc($property['deskripsi'] ?? 'Hunian berkualitas tinggi dengan struktur pondasi tiang pancang/batu kali, dinding bata merah diplester aci, rangka atap baja ringan, genteng beton/keramik, lantai granite tile 60x60, kusen aluminium, sanitair standar TOTO/setara, air PDAM/sumur bor, dan listrik PLN 2200 VA.')) ?>
                        </p>
                    </div>

                <?php else: ?>
                    <!-- Global Catalog Info -->
                    <div class="text-center py-4">
                        <i class="bi bi-building-check fs-1 text-primary mb-3 d-block"></i>
                        <h3 class="fw-bold text-dark mb-2">Katalog & Price List Kawasan Hunian</h3>
                        <p class="text-muted mb-4 max-w-600 mx-auto">
                            Daftar harga dan pilihan unit di kawasan <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>. Fasilitas terintegrasi: Security 24 Jam & CCTV, One Gate System, Underground Utilities, Clubhouse, Children Playground, dan Jogging Track.
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Footer Guarantee -->
                <div class="border-top pt-4 mt-2 text-center text-muted small">
                    <p class="mb-1">Dokumen lembar spesifikasi & price list ini diterbitkan resmi oleh tim marketing <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>.</p>
                    <p class="mb-0">Website: <strong><?= site_url() ?></strong> &bull; Email: <strong><?= esc($settings['company_email'] ?? 'marketing@grandharmoni.co.id') ?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background: #ffffff !important;
    }
    nav, footer, .d-print-none, .floating-wa-btn {
        display: none !important;
    }
    .print-container {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>

<?= $this->endSection() ?>
