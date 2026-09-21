<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="bg-primary text-white py-5">
    <div class="container py-3">
        <h1 class="fw-bold mb-2 text-white">Katalog Properti & Tipe Rumah</h1>
        <p class="lead text-white-50 mb-0">Temukan beragam pilihan tipe rumah modern dengan spesifikasi terbaik dan harga transparan.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Filter Card -->
    <div class="card card-custom border-0 p-4 mb-5 bg-white">
        <form method="GET" action="<?= site_url('properti') ?>" class="row g-3 align-items-end">
            <div class="col-lg-4 col-md-6">
                <label class="form-label fw-bold small text-muted">Cari Kata Kunci / Tipe</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="keyword" class="form-control bg-light border-start-0" placeholder="Contoh: Type 45, Cluster Lavender..." value="<?= esc($filters['keyword'] ?? '') ?>">
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-bold small text-muted">Status & Promo Unit</label>
                <select name="status" class="form-select bg-light">
                    <option value="">Semua Status & Penawaran</option>
                    <option value="Promo" <?= ($filters['status'] ?? '') === 'Promo' ? 'selected' : '' ?>>🔥 Sedang Promo Spesial</option>
                    <option value="Tersedia" <?= ($filters['status'] ?? '') === 'Tersedia' ? 'selected' : '' ?>>Tersedia (Ready)</option>
                    <option value="Booking" <?= ($filters['status'] ?? '') === 'Booking' ? 'selected' : '' ?>>Booking</option>
                    <option value="Terjual" <?= ($filters['status'] ?? '') === 'Terjual' ? 'selected' : '' ?>>Terjual (Sold Out)</option>
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-bold small text-muted">Rentang Maksimal Harga</label>
                <select name="max_price" class="form-select bg-light">
                    <option value="">Semua Harga</option>
                    <option value="700000000" <?= ($filters['max_price'] ?? '') === '700000000' ? 'selected' : '' ?>>Hingga Rp 700 Juta</option>
                    <option value="1500000000" <?= ($filters['max_price'] ?? '') === '1500000000' ? 'selected' : '' ?>>Hingga Rp 1.5 Milyar</option>
                    <option value="2500000000" <?= ($filters['max_price'] ?? '') === '2500000000' ? 'selected' : '' ?>>Hingga Rp 2.5 Milyar</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                <a href="<?= site_url('properti') ?>" class="btn btn-light py-2 rounded-3 text-muted" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>

            <!-- Quick Filter Badges -->
            <div class="col-12 mt-3 pt-3 border-top">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <small class="fw-bold text-muted me-1"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Pencarian Cepat:</small>
                    <a href="<?= site_url('properti?status=Promo') ?>" class="btn btn-sm <?= ($filters['status'] ?? '') === 'Promo' ? 'btn-danger' : 'btn-outline-danger' ?> rounded-pill px-3 py-1 fw-bold shadow-sm">
                        <i class="bi bi-fire me-1"></i> Unit Sedang Promo
                    </a>
                    <a href="<?= site_url('properti?status=Tersedia') ?>" class="btn btn-sm <?= ($filters['status'] ?? '') === 'Tersedia' ? 'btn-success' : 'btn-outline-success' ?> rounded-pill px-3 py-1">
                        <i class="bi bi-check-circle me-1"></i> Unit Tersedia
                    </a>
                    <a href="<?= site_url('properti?max_price=700000000') ?>" class="btn btn-sm <?= ($filters['max_price'] ?? '') === '700000000' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3 py-1">
                        &le; Rp 700 Juta
                    </a>
                    <a href="<?= site_url('properti?max_price=1500000000') ?>" class="btn btn-sm <?= ($filters['max_price'] ?? '') === '1500000000' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3 py-1">
                        &le; Rp 1.5 Milyar
                    </a>
                    <?php if (!empty($filters['status']) || !empty($filters['keyword']) || !empty($filters['max_price'])): ?>
                        <a href="<?= site_url('properti') ?>" class="btn btn-sm btn-link text-muted text-decoration-none py-1 ms-auto">
                            <i class="bi bi-x-circle me-1"></i> Bersihkan Semua Filter
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Properties Grid -->
    <div class="row g-4">
        <?php if (empty($properties)): ?>
            <div class="col-12 text-center py-5">
                <div class="py-5 bg-white rounded-4 shadow-sm">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <h5 class="fw-bold mt-3 text-dark">Tidak ada properti yang cocok dengan filter Anda</h5>
                    <p class="text-muted small">Coba ubah kata kunci atau reset filter pencarian di atas.</p>
                    <a href="<?= site_url('properti') ?>" class="btn btn-outline-primary rounded-pill px-4 mt-2">Lihat Semua Properti</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($properties as $prop): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-custom h-100 overflow-hidden bg-white">
                        <div class="position-relative">
                            <?php 
                            $imgSrc = $prop['primary_image'] ?? '';
                            if (empty($imgSrc)) {
                                $imgSrc = 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=600&auto=format&fit=crop&q=80';
                            } elseif (!str_starts_with($imgSrc, 'http')) {
                                $imgSrc = base_url('uploads/properties/' . $imgSrc);
                            }
                            ?>
                            <img src="<?= esc($imgSrc) ?>" class="card-img-top" alt="Rumah <?= esc($prop['title']) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" style="height: 240px; object-fit: cover;">
                            
                            <span class="position-absolute top-0 start-0 m-3 badge <?= $prop['status_badge'] ?? 'bg-success' ?> rounded-pill px-3 py-2 shadow-sm">
                                <i class="bi bi-tag-fill me-1"></i> <?= esc($prop['status_label'] ?? $prop['status']) ?>
                            </span>

                            <?php if (!empty($prop['is_promo'])): ?>
                                <button type="button" class="position-absolute top-0 end-0 m-3 badge bg-danger text-white rounded-pill px-3 py-2 border-0 shadow-sm btn-unit-promo-trigger" data-title="<?= esc($prop['title']) ?>" data-price="Rp <?= number_format($prop['harga'], 0, ',', '.') ?>" data-promo-title="<?= esc($prop['promo_title'] ?: 'Promo Spesial Unit') ?>" data-promo-desc="<?= esc($prop['promo_desc'] ?: 'Free BPHTB, Subsidi DP & Angsuran KPR.') ?>" data-slug="<?= esc($prop['slug']) ?>" data-img="<?= esc($imgSrc) ?>" title="Klik untuk lihat promo unit ini">
                                    <i class="bi bi-fire text-warning me-1"></i> PROMO
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold card-title mb-1 text-dark"><?= esc($prop['title']) ?></h5>
                            <div class="fs-4 fw-extrabold text-primary mb-3">
                                Rp <?= number_format($prop['harga'], 0, ',', '.') ?>
                            </div>
                            <p class="text-muted small mb-3 flex-grow-1">
                                <?= character_limiter(strip_tags($prop['deskripsi'] ?? ''), 100) ?>
                            </p>
                            
                            <div class="bg-light p-2 rounded-3 mb-3 d-flex justify-content-around text-center small text-muted">
                                <div>
                                    <i class="bi bi-aspect-ratio text-primary d-block fs-6"></i>
                                    <span>LT <?= esc($prop['luas_tanah']) ?> m²</span>
                                </div>
                                <div class="border-start"></div>
                                <div>
                                    <i class="bi bi-building text-primary d-block fs-6"></i>
                                    <span>LB <?= esc($prop['luas_bangunan']) ?> m²</span>
                                </div>
                                <div class="border-start"></div>
                                <div>
                                    <i class="bi bi-door-closed text-primary d-block fs-6"></i>
                                    <span><?= esc($prop['spesifikasi_kamar'] ?: '2 KT / 1 KM') ?></span>
                                </div>
                            </div>

                            <?php if (!empty($prop['is_promo'])): ?>
                                <button type="button" class="btn btn-warning btn-sm rounded-pill w-100 fw-bold mb-2 shadow-sm text-dark btn-unit-promo-trigger" data-title="<?= esc($prop['title']) ?>" data-price="Rp <?= number_format($prop['harga'], 0, ',', '.') ?>" data-promo-title="<?= esc($prop['promo_title'] ?: 'Promo Spesial Unit') ?>" data-promo-desc="<?= esc($prop['promo_desc'] ?: 'Free BPHTB, Subsidi DP & Angsuran KPR.') ?>" data-slug="<?= esc($prop['slug']) ?>" data-img="<?= esc($imgSrc) ?>">
                                    <i class="bi bi-gift-fill text-danger me-1"></i> Cek Promo Unit
                                </button>
                            <?php endif; ?>

                            <a href="<?= site_url('properti/' . $prop['slug']) ?>" class="btn btn-primary w-100 rounded-pill py-2">
                                Lihat Detail & Simulasi KPR
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
