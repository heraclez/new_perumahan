<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>

<!-- Header Section -->
<section class="py-5 bg-light border-bottom">
    <div class="container py-3">
        <div class="row align-items-center justify-content-between g-4">
            <div class="col-lg-7">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb small mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Artikel &amp; Berita</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-extrabold text-dark tracking-tight mb-2">
                    Artikel, Tips KPR &amp; Berita
                </h1>
                <p class="lead text-muted fs-6 mb-0">
                    Panduan lengkap seputar pembelian rumah pertama, simulasi KPR bank, tips renovasi, dan pembaruan progres kawasan.
                </p>
            </div>
            <div class="col-lg-5">
                <form action="<?= site_url('blog') ?>" method="GET" class="position-relative">
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="cari" class="form-control border-0" placeholder="Cari tips KPR, legalitas..." value="<?= esc($currentKeyword ?? '') ?>">
                        <button class="btn btn-primary px-4 fw-bold" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Category Pills -->
        <?php if (!empty($categories)): ?>
        <div class="d-flex flex-wrap gap-2 mt-4 pt-2 border-top">
            <a href="<?= site_url('blog') ?>" class="btn btn-sm rounded-pill px-3 <?= empty($currentCategory) ? 'btn-primary' : 'btn-white border text-dark' ?>">
                Semua Artikel
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= site_url('blog?kategori=' . urlencode($cat['category'])) ?>" class="btn btn-sm rounded-pill px-3 <?= ($currentCategory === $cat['category']) ? 'btn-primary' : 'btn-white border text-dark' ?>">
                    <?= esc($cat['category']) ?> <span class="badge bg-light text-secondary ms-1 rounded-pill"><?= $cat['total'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Main Articles Grid & Sidebar -->
<div class="container py-5">
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <?php if (!empty($currentKeyword) || !empty($currentCategory)): ?>
                <div class="d-flex align-items-center justify-content-between mb-4 p-3 bg-light rounded-3 border">
                    <div class="small">
                        Menampilkan hasil: 
                        <?php if (!empty($currentCategory)): ?><strong>Kategori "<?= esc($currentCategory) ?>"</strong><?php endif; ?>
                        <?php if (!empty($currentKeyword)): ?><strong>Kata Kunci "<?= esc($currentKeyword) ?>"</strong><?php endif; ?>
                    </div>
                    <a href="<?= site_url('blog') ?>" class="btn btn-sm btn-link text-decoration-none p-0 text-danger">Reset Filter</a>
                </div>
            <?php endif; ?>

            <?php if (empty($articles)): ?>
                <div class="text-center py-5 bg-white rounded-4 border p-5">
                    <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
                    <h5 class="fw-bold text-dark">Tidak ada artikel yang ditemukan</h5>
                    <p class="text-muted small">Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.</p>
                    <a href="<?= site_url('blog') ?>" class="btn btn-outline-primary rounded-pill px-4 btn-sm">Lihat Semua Artikel</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($articles as $art): ?>
                        <div class="col-md-6">
                            <div class="card card-custom h-100 border-0 shadow-sm overflow-hidden bg-white">
                                <a href="<?= site_url('blog/' . $art['slug']) ?>" class="d-block overflow-hidden position-relative" style="height: 200px;">
                                    <?php 
                                    $coverImg = $art['featured_image'] ?: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&auto=format&fit=crop&q=80';
                                    ?>
                                    <img src="<?= esc($coverImg) ?>" alt="<?= esc($art['title']) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" class="w-100 h-100 object-fit-cover" style="transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    <span class="position-absolute top-0 start-0 m-3 badge bg-primary rounded-pill px-2.5 py-1.5 shadow-sm" style="font-size: 0.72rem;">
                                        <?= esc($art['category']) ?>
                                    </span>
                                </a>
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="text-muted small mb-2 d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                                        <span><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($art['created_at'])) ?></span>
                                        <span>&bull;</span>
                                        <span><i class="bi bi-eye me-1"></i><?= number_format($art['views_count'] ?? 0) ?> views</span>
                                    </div>
                                    <h5 class="card-title fw-bold mb-2">
                                        <a href="<?= site_url('blog/' . $art['slug']) ?>" class="text-dark text-decoration-none">
                                            <?= esc($art['title']) ?>
                                        </a>
                                    </h5>
                                    <p class="text-muted small mb-4 flex-grow-1">
                                        <?= esc($art['excerpt']) ?>
                                    </p>
                                    <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                        <small class="text-muted"><i class="bi bi-person me-1"></i><?= esc($art['author_name']) ?></small>
                                        <a href="<?= site_url('blog/' . $art['slug']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pager && $pager->getPageCount() > 1): ?>
                    <div class="d-flex justify-content-center mt-5">
                        <?= $pager->links('default', 'bootstrap_pagination') ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Promo / KPR CTA Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 text-white mb-4" style="background: linear-gradient(135deg, var(--bs-primary), var(--bs-secondary));">
                <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold mb-3 d-inline-block align-self-start" style="font-size: 0.75rem;">
                    <i class="bi bi-calculator-fill me-1"></i> SIMULASI GRATIS
                </span>
                <h4 class="fw-bold text-white mb-2">Tertarik Memiliki Rumah Sendiri?</h4>
                <p class="text-white-50 small mb-4">
                    Hitung estimasi angsuran bulanan KPR dan cicilan uang muka sesuai penghasilan Anda secara gratis.
                </p>
                <div class="d-grid gap-2">
                    <a href="<?= site_url('kpr-calculator') ?>" class="btn btn-light text-primary fw-bold rounded-pill">
                        Buka Kalkulator KPR
                    </a>
                    <a href="<?= site_url('properti') ?>" class="btn btn-outline-light rounded-pill">
                        Lihat Pilihan Tipe Rumah
                    </a>
                </div>
            </div>

            <!-- Recent Articles Widget -->
            <?php if (!empty($recentArticles)): ?>
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-fire text-danger me-2"></i>Artikel Terbaru
                </h6>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($recentArticles as $rArt): ?>
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= site_url('blog/' . $rArt['slug']) ?>" class="flex-shrink-0">
                                <img src="<?= esc($rArt['featured_image'] ?: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=200&auto=format&fit=crop&q=80') ?>" alt="<?= esc($rArt['title']) ?>" loading="lazy" decoding="async" class="rounded-3 object-fit-cover shadow-xs" style="width: 64px; height: 64px;">
                            </a>
                            <div class="flex-grow-1">
                                <span class="badge bg-light text-primary border" style="font-size: 0.65rem;"><?= esc($rArt['category']) ?></span>
                                <h6 class="fw-bold mb-0 mt-1" style="font-size: 0.85rem; line-height: 1.35;">
                                    <a href="<?= site_url('blog/' . $rArt['slug']) ?>" class="text-dark text-decoration-none">
                                        <?= character_limiter(esc($rArt['title']), 45) ?>
                                    </a>
                                </h6>
                                <small class="text-muted" style="font-size: 0.72rem;"><?= date('d M Y', strtotime($rArt['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- WhatsApp Consultation Box -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white text-center">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
                    <i class="bi bi-whatsapp fs-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Punya Pertanyaan Spesifik?</h6>
                <p class="text-muted small mb-3">Konsultasikan kebutuhan rumah impian Anda langsung dengan tim konsultan kami.</p>
                <a href="https://wa.me/<?= esc($settings['company_whatsapp'] ?? '') ?>?text=Halo%20<?= urlencode($settings['company_name'] ?? 'Grand Harmoni') ?>,%20saya%20ingin%20konsultasi%20KPR." target="_blank" class="btn btn-success rounded-pill fw-bold w-100">
                    <i class="bi bi-whatsapp me-1"></i> Chat via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
