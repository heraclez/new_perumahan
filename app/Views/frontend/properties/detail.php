<?= $this->extend('layouts/frontend') ?>

<?= $this->section('og_tags') ?>
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= esc($og_title) ?>">
    <meta property="og:description" content="<?= esc($og_description) ?>">
    <meta property="og:image" content="<?= esc($og_image) ?>">
    <meta property="og:url" content="<?= esc($og_url) ?>">
    <meta property="og:site_name" content="<?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($og_title) ?>">
    <meta name="twitter:description" content="<?= esc($og_description) ?>">
    <meta name="twitter:image" content="<?= esc($og_image) ?>">
<?= $this->endSection() ?>

<?= $this->section('schema_json_ld') ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SingleFamilyResidence",
  "name": <?= json_encode($property['title']) ?>,
  "description": <?= json_encode(strip_tags($property['deskripsi'] ?? '')) ?>,
  "url": <?= json_encode(current_url()) ?>,
  "image": <?= json_encode($og_image) ?>,
  "numberOfRooms": <?= json_encode($property['spesifikasi_kamar'] ?? '') ?>,
  "floorSize": {
    "@type": "QuantitativeValue",
    "value": <?= json_encode($property['luas_bangunan'] ?? 0) ?>,
    "unitCode": "MTK"
  },
  "offers": {
    "@type": "Offer",
    "price": <?= json_encode($property['harga']) ?>,
    "priceCurrency": "IDR",
    "availability": "https://schema.org/InStock",
    "validFrom": <?= json_encode(date('Y-m-d')) ?>
  },
  "address": {
    "@type": "PostalAddress",
    "streetAddress": <?= json_encode($settings['company_address'] ?? 'Kawasan Perumahan') ?>,
    "addressCountry": "ID"
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Beranda",
      "item": <?= json_encode(site_url('/')) ?>
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Katalog Properti",
      "item": <?= json_encode(site_url('properti')) ?>
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": <?= json_encode($property['title']) ?>,
      "item": <?= json_encode(current_url()) ?>
    }
  ]
}
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('properti') ?>" class="text-decoration-none">Katalog Properti</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($property['title']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Left: Image Gallery & Description & KPR Calculator -->
        <div class="col-lg-8">
            <!-- Gallery Section -->
            <div class="card card-custom border-0 overflow-hidden bg-white mb-4">
                <div class="position-relative">
                    <?php 
                    $mainImg = 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=1000&auto=format&fit=crop&q=80';
                    if (!empty($property['images'][0]['image_name'])) {
                        $first = $property['images'][0]['image_name'];
                        $mainImg = str_starts_with($first, 'http') ? $first : base_url('uploads/properties/' . $first);
                    }
                    ?>
                    <img id="mainGalleryImg" src="<?= esc($mainImg) ?>" class="w-100 object-fit-cover" alt="Fasad Utama Rumah <?= esc($property['title']) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" fetchpriority="high" decoding="async" style="height: 440px; cursor: pointer;" onclick="openLightbox()">
                    
                    <span class="position-absolute top-0 start-0 m-3 badge <?= $property['status_badge'] ?? 'bg-success' ?> rounded-pill px-3 py-2 fs-6 shadow-sm">
                        <i class="bi bi-tag-fill me-1"></i> <?= esc($property['status_label'] ?? $property['status']) ?>
                    </span>

                    <button type="button" class="btn btn-sm btn-dark bg-opacity-75 text-white rounded-pill px-3 py-1 position-absolute bottom-0 end-0 m-3 shadow-sm border-0 d-flex align-items-center gap-1" onclick="openLightbox()" style="backdrop-filter: blur(4px); font-size: 0.8rem;">
                        <i class="bi bi-arrows-fullscreen"></i> <span>Perbesar Foto</span>
                    </button>
                </div>

                <!-- Thumbnail Navigation -->
                <?php if (!empty($property['images']) && count($property['images']) > 1): ?>
                    <div class="p-3 bg-light border-top d-flex gap-2 overflow-x-auto">
                        <?php foreach ($property['images'] as $idx => $img): ?>
                            <?php 
                            $thumbSrc = str_starts_with($img['image_name'], 'http') ? $img['image_name'] : base_url('uploads/properties/' . $img['image_name']);
                            ?>
                            <img src="<?= esc($thumbSrc) ?>" alt="Galeri Rumah <?= esc($property['title']) ?> Foto <?= $idx + 1 ?>" loading="lazy" decoding="async" class="gallery-thumb rounded-3 border <?= $idx === 0 ? 'border-primary border-3' : '' ?>" style="width: 84px; height: 60px; object-fit: cover; cursor: pointer;" onclick="changeGalleryImage('<?= esc($thumbSrc) ?>', this)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($property['is_promo'])): ?>
                <!-- Unit Promo Highlight Banner -->
                <div class="card border-0 p-4 mb-4 text-white shadow-sm rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #c02424 0%, #dc3545 40%, #f77f00 100%);">
                    <div class="position-relative z-2">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-white text-danger fw-bold rounded-pill px-3 py-1 shadow-sm">
                                <i class="bi bi-fire text-warning me-1"></i> PROMO SPESIAL UNIT
                            </span>
                            <span class="badge bg-black bg-opacity-25 text-white rounded-pill px-2 py-1 small">
                                <i class="bi bi-clock-history me-1"></i> Kuota Promo Terbatas
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2 text-white"><?= esc($property['promo_title'] ?: 'Promo Eksklusif Khusus Unit Ini!') ?></h4>
                        <p class="mb-3 text-white text-opacity-90 small" style="line-height: 1.6;">
                            <?= nl2br(esc($property['promo_desc'] ?: 'Dapatkan penawaran harga spesial, diskon DP, dan subsidi KPR khusus booking hari ini.')) ?>
                        </p>
                        <?php 
                        $compName = $settings['company_name'] ?? 'Grand Harmoni Residence';
                        $compWa   = $settings['company_whatsapp'] ?? '6281234567890';
                        $waText   = 'Halo ' . $compName . ', saya tertarik dan ingin klaim promo untuk ' . $property['title'] . ' (' . ($property['promo_title'] ?: 'Promo Spesial') . '). Mohon info selengkapnya.';
                        ?>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="https://wa.me/<?= esc($compWa) ?>?text=<?= urlencode($waText) ?>" target="_blank" class="btn btn-light text-danger fw-bold rounded-pill px-4 py-2 shadow-sm">
                                <i class="bi bi-whatsapp me-1 text-success"></i> Klaim Promo Unit Ini via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Property Overview & Specs -->
            <div class="card card-custom border-0 p-4 bg-white mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-bottom pb-3 mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1"><?= esc($property['title']) ?></h2>
                        <span class="text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= esc($settings['company_address'] ?? 'Kawasan Grand Harmoni') ?></span>
                    </div>
                    <div class="text-md-end">
                        <small class="text-muted d-block">Harga Mulai</small>
                        <h3 class="fw-extrabold text-primary mb-0">Rp <?= number_format($property['harga'], 0, ',', '.') ?></h3>
                    </div>
                </div>

                <!-- Specs Grid -->
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Spesifikasi Utama</h5>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <i class="bi bi-aspect-ratio fs-3 text-primary d-block mb-1"></i>
                            <small class="text-muted d-block">Luas Tanah</small>
                            <strong class="text-dark fs-6"><?= esc($property['luas_tanah']) ?> m²</strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <i class="bi bi-building fs-3 text-primary d-block mb-1"></i>
                            <small class="text-muted d-block">Luas Bangunan</small>
                            <strong class="text-dark fs-6"><?= esc($property['luas_bangunan']) ?> m²</strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <i class="bi bi-door-closed fs-3 text-primary d-block mb-1"></i>
                            <small class="text-muted d-block">Kamar Tidur/Mandi</small>
                            <strong class="text-dark fs-6"><?= esc($property['spesifikasi_kamar'] ?: '2 KT / 1 KM') ?></strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <i class="bi bi-award fs-3 text-primary d-block mb-1"></i>
                            <small class="text-muted d-block">Legalitas</small>
                            <strong class="text-dark fs-6">SHM + PBG</strong>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <h5 class="fw-bold mb-3"><i class="bi bi-file-text-fill text-primary me-2"></i>Deskripsi Properti</h5>
                <div class="text-secondary lh-lg mb-0">
                    <?= nl2br(esc($property['deskripsi'] ?? 'Hunian berkualitas tinggi dengan desain arsitektur modern minimalis.')) ?>
                </div>
            </div>

            <!-- Ketersediaan Kavling di Master Siteplan (Sinkronisasi Real-Time) -->
            <?php if (!empty($mappedPins)): ?>
                <div class="card card-custom border-0 p-4 bg-white mb-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="badge-primary-soft p-2 rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-map-fill fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Ketersediaan Unit Kavling di Master Siteplan</h5>
                                <small class="text-muted">Status ketersediaan nomor blok fisik untuk tipe rumah ini</small>
                            </div>
                        </div>
                        <?php 
                        $firstSiteplanId = $mappedPins[0]['siteplan_id'] ?? null;
                        ?>
                        <?php if ($firstSiteplanId): ?>
                            <a href="<?= site_url('siteplan/' . $firstSiteplanId) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
                                <i class="bi bi-geo-alt-fill"></i> Buka di Peta Siteplan
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($mappedPins as $mp): ?>
                            <?php 
                            $statusClass = $mp['status'] === 'Tersedia' ? 'border-success bg-success-subtle text-success' : ($mp['status'] === 'Booking' ? 'border-warning bg-warning-subtle text-dark' : 'border-danger bg-danger-subtle text-danger');
                            $dotClass = $mp['status'] === 'Tersedia' ? 'bg-success' : ($mp['status'] === 'Booking' ? 'bg-warning' : 'bg-danger');
                            ?>
                            <div class="border rounded-3 p-2 px-3 d-flex align-items-center gap-2 <?= $statusClass ?>" style="font-size: 0.85rem;">
                                <span class="rounded-circle <?= $dotClass ?>" style="width: 8px; height: 8px; display: inline-block;"></span>
                                <strong><?= esc($mp['kavling_number']) ?></strong>
                                <span class="badge bg-white text-dark rounded-pill border" style="font-size: 0.72rem;"><?= esc($mp['status']) ?></span>
                                <?php if (!empty($mp['notes'])): ?>
                                    <small class="text-muted" style="font-size: 0.72rem;">(<?= esc($mp['notes']) ?>)</small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- KALKULATOR KPR (Client-Side jQuery - Rumah123 Logic) -->
            <!-- ========================================== -->
            <div class="card card-custom border-0 p-4 bg-white mb-4" id="kprCalculatorSection">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-calculator fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Kalkulator Simulasi KPR Pintar</h4>
                        <small class="text-muted">Simulasi angsuran bulanan, estimasi DP pertama & analisis Debt Service Ratio (DSR)</small>
                    </div>
                </div>
                <hr class="mb-4">

                <div class="row g-4">
                    <!-- Calculator Inputs -->
                    <div class="col-md-6">
                        <!-- Harga Properti -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Harga Properti (Rp)</label>
                            <input type="number" id="kprHarga" class="form-control form-control-lg fw-bold text-dark" value="<?= esc($property['harga']) ?>" step="10000000">
                        </div>

                        <!-- Uang Muka (DP) - Dual-Bound Two-Way Sync -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold small text-muted mb-0">Uang Muka (DP)</label>
                                <span class="badge bg-light text-primary border" id="kprDpPercentBadge">10%</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-8">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number" id="kprDpNominal" class="form-control fw-semibold" value="<?= $property['harga'] * 0.10 ?>" step="1000000">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="number" id="kprDpPercent" class="form-control fw-semibold" value="10" min="0" max="90" step="1">
                                        <span class="input-group-text bg-light">%</span>
                                    </div>
                                </div>
                            </div>
                            <input type="range" class="form-range mt-2" id="kprDpSlider" min="0" max="90" step="1" value="10">
                        </div>

                        <!-- Suku Bunga & Tenor -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Suku Bunga Fix (%/th)</label>
                                <div class="input-group">
                                    <input type="number" id="kprBunga" class="form-control fw-semibold" value="5.5" min="1" max="25" step="0.1">
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Jangka Waktu (Tenor)</label>
                                <div class="input-group">
                                    <input type="number" id="kprTenor" class="form-control fw-semibold" value="20" min="1" max="30" step="1">
                                    <span class="input-group-text bg-light">Tahun</span>
                                </div>
                            </div>
                        </div>

                        <!-- Gaji Bulanan (Untuk DSR Check) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Penghasilan Bersih Bulanan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" id="kprGaji" class="form-control fw-semibold" value="15000000" step="500000">
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Digunakan untuk menguji kelayakan cicilan (Rasio DSR ideal ≤ 30%)</small>
                        </div>
                    </div>

                    <!-- Calculator Results Output Card -->
                    <div class="col-md-6">
                        <div class="bg-light p-4 rounded-4 h-100 d-flex flex-column justify-content-between border">
                            <div>
                                <small class="text-muted text-uppercase fw-bold letter-spacing-1">Estimasi Angsuran KPR</small>
                                <h3 class="display-6 fw-extrabold text-primary my-2" id="kprCicilanOutput">Rp 0</h3>
                                <small class="text-muted d-block mb-3">Per bulan (Fixed rate)</small>
                                
                                <div class="border-top pt-3">
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>Pokok Pinjaman KPR:</span>
                                        <strong class="text-dark" id="kprPinjamanOutput">Rp 0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>Asumsi Biaya Notaris & Bank (5%):</span>
                                        <strong class="text-dark" id="kprBiayaNotarisOutput">Rp 0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between small text-dark fw-bold mb-3 border-top pt-2">
                                        <span>Estimasi Pembayaran Pertama (DP + Cicilan 1 + Biaya Bank):</span>
                                        <span class="text-primary" id="kprTotalAwalOutput">Rp 0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- DSR Kelayakan Badge Box -->
                            <div id="kprDsrBox" class="alert alert-success border-0 mb-0 p-3 rounded-3 mt-3">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-shield-check fs-4" id="kprDsrIcon"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1" id="kprDsrTitle">Status Keuangan: Sehat</h6>
                                        <p class="mb-0 small" id="kprDsrDesc">Beban cicilan 24% dari gaji bulanan Anda (dibawah batas aman 30%). Potensi persetujuan bank sangat tinggi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Action Sidebar & Lead Magnet -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 90px; z-index: 10;">
                <div class="card card-custom border-0 p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3">Tertarik dengan Unit Ini?</h5>
                    <p class="text-muted small mb-4">Dapatkan brosur spesifikasi lengkap, simulasi cicilan resmi dari bank, atau konsultasi gratis via WhatsApp.</p>

                    <!-- Lead Magnet Trigger Button -->
                    <button type="button" class="btn btn-primary w-100 py-3 rounded-pill mb-3 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#modalLeadMagnet">
                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                        <span>Unduh E-Brosur & Price List</span>
                    </button>

                    <!-- Direct WhatsApp Button -->
                    <?php 
                    $waText = "Halo " . ($settings['company_name'] ?? 'Grand Harmoni') . ", saya ingin konsultasi mengenai properti '" . $property['title'] . "' (" . site_url('properti/' . $property['slug']) . "). Mohon info ketersediaan unit dan jadwal survey.";
                    $waUrl = "https://wa.me/" . ($settings['company_whatsapp'] ?? '6281234567890') . "?text=" . urlencode($waText);
                    ?>
                    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-outline-success w-100 py-3 rounded-pill d-flex align-items-center justify-content-center gap-2 fw-bold">
                        <i class="bi bi-whatsapp fs-5"></i>
                        <span>Chat WhatsApp Marketing</span>
                    </a>

                    <div class="mt-4 pt-3 border-top text-center">
                        <small class="text-muted d-block mb-2">Bagikan Tipe Rumah Ini</small>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($property['title'] . ' - Cek rumah ini: ' . current_url()) ?>" target="_blank" class="btn btn-sm btn-light rounded-circle p-2 text-success" title="Bagikan ke WhatsApp"><i class="bi bi-whatsapp fs-6"></i></a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" class="btn btn-sm btn-light rounded-circle p-2 text-primary" title="Bagikan ke Facebook"><i class="bi bi-facebook fs-6"></i></a>
                            <button type="button" class="btn btn-sm btn-light rounded-circle p-2 text-dark" onclick="copyPropertyUrl()" title="Salin Tautan"><i class="bi bi-link-45deg fs-6"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Related Properties Mini Card -->
                <?php if (!empty($relatedProperties)): ?>
                    <div class="card card-custom border-0 p-3 bg-white">
                        <h6 class="fw-bold mb-3 text-dark">Tipe Rumah Lainnya</h6>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($relatedProperties as $rel): ?>
                                <?php if ($rel['id'] != $property['id']): ?>
                                    <a href="<?= site_url('properti/' . $rel['slug']) ?>" class="d-flex gap-3 text-decoration-none text-dark align-items-center">
                                        <?php 
                                        $relImg = $rel['primary_image'] ?? '';
                                        if (empty($relImg)) {
                                            $relImg = 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=200&auto=format&fit=crop&q=80';
                                        } elseif (!str_starts_with($relImg, 'http')) {
                                            $relImg = base_url('uploads/properties/' . $relImg);
                                        }
                                        ?>
                                        <img src="<?= esc($relImg) ?>" class="rounded-3" style="width: 70px; height: 55px; object-fit: cover;" alt="Tipe Rumah <?= esc($rel['title']) ?>" loading="lazy" decoding="async">
                                        <div>
                                            <h6 class="mb-0 fw-bold small text-truncate" style="max-width: 180px;"><?= esc($rel['title']) ?></h6>
                                            <span class="text-primary small fw-bold">Rp <?= number_format($rel['harga'], 0, ',', '.') ?></span>
                                        </div>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MOBILE STICKY BOTTOM ACTION BAR (Thumb-friendly CTA) -->
<!-- ========================================== -->
<div class="d-md-none fixed-bottom bg-white border-top shadow-lg py-2 px-3 z-3" style="border-top-left-radius: 16px; border-top-right-radius: 16px; box-shadow: 0 -4px 20px rgba(0,0,0,0.08) !important;">
    <div class="d-flex align-items-center justify-content-between gap-2">
        <div class="lh-sm">
            <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Harga Mulai</span>
            <span class="fw-extrabold text-primary" style="font-size: 1.05rem;">
                Rp <?= number_format($property['harga'], 0, ',', '.') ?>
            </span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalLeadMagnet" style="font-size: 0.8rem;">
                <i class="bi bi-file-earmark-pdf-fill"></i> <span>Brosur</span>
            </button>
            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-success btn-sm rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-1 shadow-sm" style="font-size: 0.8rem;">
                <i class="bi bi-whatsapp"></i> <span>Tanya WA</span>
            </a>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- FULLSCREEN LIGHTBOX MODAL (Image Zoom Preview) -->
<!-- ========================================== -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 position-relative text-center">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3 bg-dark bg-opacity-75 p-2 rounded-circle shadow" data-bs-dismiss="modal" aria-label="Close"></button>
                <img id="lightboxImg" src="" alt="Fullscreen Foto Rumah" class="img-fluid rounded-4 shadow-lg" style="max-height: 85vh; object-fit: contain; background: #111;">
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- LEAD MAGNET MODAL (AJAX Form for Download Brosur) -->
<!-- ========================================== -->
<div class="modal fade" id="modalLeadMagnet" tabindex="-1" aria-labelledby="modalLeadMagnetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                    <h5 class="modal-title fw-bold mb-0" id="modalLeadMagnetLabel">Unduh E-Brosur & Price List</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Silakan lengkapi data di bawah ini. File e-brosur resmi untuk unit <strong><?= esc($property['title']) ?></strong> akan langsung diunduh ke perangkat Anda.
                </p>

                <div id="leadAlertContainer"></div>

                <form id="formLeadMagnet">
                    <?= csrf_field() ?>
                    <input type="hidden" name="property_id" value="<?= esc($property['id']) ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Lengkap Anda <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="nama_prospek" id="leadNama" class="form-control" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="invalid-feedback" id="err_nama_prospek"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-muted"></i></span>
                            <input type="tel" name="no_wa" id="leadWa" class="form-control" placeholder="Contoh: 081234567890" required>
                        </div>
                        <div class="invalid-feedback" id="err_no_wa"></div>
                        <small class="text-muted" style="font-size: 0.72rem;">* Privasi terjaga, nomor Anda hanya digunakan untuk konfirmasi info unit.</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 rounded-pill fw-bold" id="btnSubmitLead">
                            <i class="bi bi-download me-1"></i> Verifikasi & Unduh Brosur Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Gallery Image Switcher
    function changeGalleryImage(src, element) {
        $('#mainGalleryImg').attr('src', src);
        $('.gallery-thumb').removeClass('border-primary border-3');
        $(element).addClass('border-primary border-3');
    }

    // Fullscreen Lightbox Preview
    function openLightbox() {
        var currentSrc = $('#mainGalleryImg').attr('src');
        if (currentSrc) {
            $('#lightboxImg').attr('src', currentSrc);
            var modalEl = document.getElementById('lightboxModal');
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    }

    // Copy Property Link with Clean Toast Feedback
    function copyPropertyUrl() {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(window.location.href).then(function() {
                if (typeof window.showToast === 'function') {
                    window.showToast('Tautan rumah berhasil disalin ke clipboard!', 'bi-check2-circle', 'text-success');
                }
            }).catch(function() {
                fallbackCopyUrl();
            });
        } else {
            fallbackCopyUrl();
        }
    }

    function fallbackCopyUrl() {
        var dummy = document.createElement('input');
        document.body.appendChild(dummy);
        dummy.value = window.location.href;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        if (typeof window.showToast === 'function') {
            window.showToast('Tautan rumah berhasil disalin ke clipboard!', 'bi-check2-circle', 'text-success');
        }
    }

    $(document).ready(function() {
        // Safe-area padding for mobile sticky bottom bar
        $('body').addClass('has-bottom-bar');
        // ==========================================
        // 1. CLIENT-SIDE KPR CALCULATOR (RUMAH123 LOGIC)
        // ==========================================
        function calculateKpr() {
            var harga = parseFloat($('#kprHarga').val()) || 0;
            var dpNominal = parseFloat($('#kprDpNominal').val()) || 0;
            var bungaAnnual = parseFloat($('#kprBunga').val()) || 0;
            var tenorYear = parseInt($('#kprTenor').val()) || 0;
            var gaji = parseFloat($('#kprGaji').val()) || 0;

            if (harga <= 0 || tenorYear <= 0) {
                return;
            }

            // Pokok Pinjaman (P)
            var pinjaman = Math.max(0, harga - dpNominal);

            // Bunga Bulanan (r) & Jumlah Bulan (n)
            var r = (bungaAnnual / 100) / 12;
            var n = tenorYear * 12;

            // Rumus Anuitas: Cicilan = P * [ r * (1 + r)^n ] / [ (1 + r)^n - 1 ]
            var cicilanPerBulan = 0;
            if (r > 0) {
                var factor = Math.pow(1 + r, n);
                cicilanPerBulan = pinjaman * (r * factor) / (factor - 1);
            } else {
                cicilanPerBulan = pinjaman / n;
            }

            // Estimasi Biaya Notaris, BPHTB & Bank (Asumsi 5% dari harga properti)
            var biayaBankNotaris = harga * 0.05;

            // Estimasi Pembayaran Pertama = DP + Angsuran ke-1 + Biaya Notaris/Bank
            var totalPembayaranAwal = dpNominal + cicilanPerBulan + biayaBankNotaris;

            // Format Rupiah
            function formatRp(val) {
                return 'Rp ' + Math.round(val).toLocaleString('id-ID');
            }

            $('#kprCicilanOutput').text(formatRp(cicilanPerBulan));
            $('#kprPinjamanOutput').text(formatRp(pinjaman));
            $('#kprBiayaNotarisOutput').text(formatRp(biayaBankNotaris));
            $('#kprTotalAwalOutput').text(formatRp(totalPembayaranAwal));

            // DSR (Debt Service Ratio) Analysis
            if (gaji > 0) {
                var dsr = (cicilanPerBulan / gaji) * 100;
                var dsrFixed = dsr.toFixed(1);

                if (dsr <= 30) {
                    $('#kprDsrBox').removeClass('alert-danger alert-warning').addClass('alert-success');
                    $('#kprDsrIcon').removeClass('bi-exclamation-triangle-fill text-danger text-warning').addClass('bi-shield-check text-success');
                    $('#kprDsrTitle').text('Status Keuangan: Sehat (DSR: ' + dsrFixed + '%)');
                    $('#kprDsrDesc').text('Beban cicilan berada di bawah batas aman 30% dari penghasilan bulanan Anda. Potensi persetujuan KPR bank sangat tinggi!');
                } else if (dsr <= 40) {
                    $('#kprDsrBox').removeClass('alert-success alert-danger').addClass('alert-warning');
                    $('#kprDsrIcon').removeClass('bi-shield-check text-success text-danger').addClass('bi-exclamation-triangle-fill text-warning');
                    $('#kprDsrTitle').text('Status Keuangan: Cukup Beresiko (DSR: ' + dsrFixed + '%)');
                    $('#kprDsrDesc').text('Cicilan mencapai ' + dsrFixed + '% dari gaji. Disarankan menambah nominal DP atau memperpanjang tenor agar pengajuan disetujui bank.');
                } else {
                    $('#kprDsrBox').removeClass('alert-success alert-warning').addClass('alert-danger');
                    $('#kprDsrIcon').removeClass('bi-shield-check text-success text-warning').addClass('bi-exclamation-octagon-fill text-danger');
                    $('#kprDsrTitle').text('Status Keuangan: Beresiko Tinggi (DSR: ' + dsrFixed + '%)');
                    $('#kprDsrDesc').text('Cicilan melebihi 40% dari gaji bulanan Anda. Bank berpotensi menolak aplikasi jika tidak menambah joint income atau DP lebih besar.');
                }
            }
        }

        // Dual-Bound DP Synchronization
        $('#kprHarga').on('input', function() {
            var harga = parseFloat($(this).val()) || 0;
            var percent = parseFloat($('#kprDpPercent').val()) || 10;
            var nominal = harga * (percent / 100);
            $('#kprDpNominal').val(nominal);
            calculateKpr();
        });

        $('#kprDpNominal').on('input', function() {
            var nominal = parseFloat($(this).val()) || 0;
            var harga = parseFloat($('#kprHarga').val()) || 0;
            if (harga > 0) {
                var percent = Math.min(90, Math.max(0, (nominal / harga) * 100));
                $('#kprDpPercent').val(percent.toFixed(1));
                $('#kprDpSlider').val(percent);
                $('#kprDpPercentBadge').text(percent.toFixed(1) + '%');
            }
            calculateKpr();
        });

        $('#kprDpPercent').on('input', function() {
            var percent = parseFloat($(this).val()) || 0;
            var harga = parseFloat($('#kprHarga').val()) || 0;
            var nominal = harga * (percent / 100);
            $('#kprDpNominal').val(nominal);
            $('#kprDpSlider').val(percent);
            $('#kprDpPercentBadge').text(percent + '%');
            calculateKpr();
        });

        $('#kprDpSlider').on('input', function() {
            var percent = $(this).val();
            var harga = parseFloat($('#kprHarga').val()) || 0;
            var nominal = harga * (percent / 100);
            $('#kprDpPercent').val(percent);
            $('#kprDpNominal').val(nominal);
            $('#kprDpPercentBadge').text(percent + '%');
            calculateKpr();
        });

        $('#kprBunga, #kprTenor, #kprGaji').on('input', calculateKpr);

        // Initial trigger
        calculateKpr();

        // ==========================================
        // 2. LEAD MAGNET AJAX FORM SUBMISSION
        // ==========================================
        $('#formLeadMagnet').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#btnSubmitLead');

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Memproses data...');
            $('.invalid-feedback').text('').hide();
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: "<?= site_url('leads/store') ?>",
                type: "POST",
                data: form.serialize(),
                dataType: "json",
                success: function(res) {
                    if (res.success) {
                        $('#leadAlertContainer').html(
                            '<div class="alert alert-success border-0 small"><i class="bi bi-check-circle-fill me-1"></i> ' + res.message + '</div>'
                        );
                        form.trigger('reset');

                        // Automatically trigger download
                        setTimeout(function() {
                            window.location.href = res.download_url;
                        }, 1000);

                        setTimeout(function() {
                            var modal = bootstrap.Modal.getInstance(document.getElementById('modalLeadMagnet'));
                            if (modal) modal.hide();
                            btn.prop('disabled', false).html('<i class="bi bi-download me-1"></i> Verifikasi & Unduh Brosur Sekarang');
                            $('#leadAlertContainer').empty();
                        }, 2500);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-download me-1"></i> Verifikasi & Unduh Brosur Sekarang');
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, msg) {
                            $('#lead' + (field === 'nama_prospek' ? 'Nama' : 'Wa')).addClass('is-invalid');
                            $('#err_' + field).text(msg).show();
                        });
                    } else {
                        $('#leadAlertContainer').html(
                            '<div class="alert alert-danger border-0 small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan, silakan coba lagi.</div>'
                        );
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
