<?= $this->extend('layouts/frontend') ?>

<?= $this->section('og_tags') ?>
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= esc($og_title) ?>">
    <meta property="og:description" content="<?= esc($og_description) ?>">
    <meta property="og:image" content="<?= esc($og_image) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="<?= esc($og_url) ?>">
    <meta property="og:site_name" content="<?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>">
    <meta property="article:published_time" content="<?= esc($article['created_at']) ?>">
    <meta property="article:author" content="<?= esc($article['author_name']) ?>">
    <meta property="article:section" content="<?= esc($article['category']) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($og_title) ?>">
    <meta name="twitter:description" content="<?= esc($og_description) ?>">
    <meta name="twitter:image" content="<?= esc($og_image) ?>">
<?= $this->endSection() ?>

<?= $this->section('schema_json_ld') ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": <?= json_encode($article['title']) ?>,
  "description": <?= json_encode($article['excerpt']) ?>,
  "image": <?= json_encode($og_image) ?>,
  "datePublished": <?= json_encode($article['created_at']) ?>,
  "dateModified": <?= json_encode($article['updated_at'] ?? $article['created_at']) ?>,
  "author": {
    "@type": "Person",
    "name": <?= json_encode($article['author_name']) ?>
  },
  "publisher": {
    "@type": "Organization",
    "name": <?= json_encode($settings['company_name'] ?? 'Grand Harmoni') ?>,
    "logo": {
      "@type": "ImageObject",
      "url": <?= json_encode(!empty($settings['company_logo']) ? base_url('uploads/settings/' . $settings['company_logo']) : '') ?>
    }
  },
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": <?= json_encode(current_url()) ?>
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
      "name": "Artikel & Info",
      "item": <?= json_encode(site_url('blog')) ?>
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": <?= json_encode($article['title']) ?>,
      "item": <?= json_encode(current_url()) ?>
    }
  ]
}
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<article class="py-4 py-lg-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('blog') ?>" class="text-decoration-none">Artikel &amp; Info</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('blog?kategori=' . urlencode($article['category'])) ?>" class="text-decoration-none"><?= esc($article['category']) ?></a></li>
                <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 250px;"><?= esc($article['title']) ?></li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <!-- Header Meta -->
                <div class="mb-4">
                    <span class="badge bg-primary rounded-pill px-3 py-1.5 mb-2 fw-semibold" style="font-size: 0.8rem;">
                        <?= esc($article['category']) ?>
                    </span>
                    <h1 class="display-5 fw-extrabold text-dark tracking-tight mb-3">
                        <?= esc($article['title']) ?>
                    </h1>
                    
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small pb-3 border-bottom">
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-person-circle text-primary"></i> <?= esc($article['author_name']) ?>
                        </span>
                        <span>&bull;</span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-calendar3"></i> <?= date('d F Y', strtotime($article['created_at'])) ?>
                        </span>
                        <span>&bull;</span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-clock"></i> <?= $readingTime ?> menit baca
                        </span>
                        <span>&bull;</span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-eye"></i> <?= number_format($article['views_count'] ?? 0) ?> views
                        </span>
                    </div>
                </div>

                <!-- Featured Image -->
                <?php if (!empty($article['featured_image'])): ?>
                    <div class="mb-4 rounded-4 overflow-hidden shadow-sm">
                        <img src="<?= esc($article['featured_image']) ?>" alt="<?= esc($article['title']) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" fetchpriority="high" decoding="async" class="w-100 object-fit-cover" style="max-height: 480px;">
                    </div>
                <?php endif; ?>

                <!-- Lead Paragraph (Excerpt) -->
                <?php if (!empty($article['excerpt'])): ?>
                    <div class="p-3 p-md-4 bg-light rounded-3 border-start border-primary border-4 mb-4">
                        <p class="lead text-dark fs-6 mb-0 fst-italic">
                            "<?= esc($article['excerpt']) ?>"
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Main Content Body -->
                <div class="article-content fs-6 mb-5" style="line-height: 1.85; color: #334155;">
                    <?= $article['content'] ?>
                </div>

                <!-- Social Share Bar -->
                <div class="card bg-light border-0 rounded-3 p-3 mb-5">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <span class="fw-bold text-dark small"><i class="bi bi-share-fill text-primary me-2"></i>Bagikan artikel bermanfaat ini:</span>
                        <div class="d-flex gap-2">
                            <a href="https://wa.me/?text=<?= urlencode($article['title'] . ' ' . current_url()) ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                <i class="bi bi-facebook me-1"></i> Facebook
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="navigator.clipboard.writeText('<?= current_url() ?>'); alert('Link artikel berhasil disalin!');">
                                <i class="bi bi-link-45deg me-1"></i> Salin Link
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Rekomendasi Unit Rumah (Conversion CTA Widget) -->
                <?php if (!empty($featuredProperties)): ?>
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white mb-5 border-top border-primary border-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
                        <div>
                            <span class="text-primary fw-bold text-uppercase small letter-spacing-1">Rekomendasi Hunian Terbaik</span>
                            <h3 class="fw-bold text-dark mb-0">Tertarik Memiliki Rumah di <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>?</h3>
                            <p class="text-muted small mb-0 mt-1">Dapatkan promo subsidi DP, cicilan KPR terjangkau, dan sertifikat SHM.</p>
                        </div>
                        <a href="<?= site_url('properti') ?>" class="btn btn-outline-primary rounded-pill px-4 mt-3 mt-md-0 btn-sm">
                            Lihat Semua Tipe <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($featuredProperties as $prop): ?>
                            <?php
                            $propImg = $prop['primary_image'] ?: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=500&auto=format&fit=crop&q=80';
                            if (!str_starts_with($propImg, 'http')) {
                                $propImg = base_url('uploads/properties/' . $propImg);
                            }
                            ?>
                            <div class="col-md-4">
                                <div class="card h-100 border rounded-3 overflow-hidden shadow-2xs group-hover">
                                    <img src="<?= esc($propImg) ?>" alt="Tipe Rumah <?= esc($prop['title']) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" class="w-100 object-fit-cover" style="height: 140px;">
                                    <div class="p-3">
                                        <h6 class="fw-bold text-dark mb-1 fs-7"><?= esc($prop['title']) ?></h6>
                                        <div class="fw-extrabold text-primary small mb-2">Rp <?= number_format($prop['harga'], 0, ',', '.') ?></div>
                                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                            <span>LT <?= esc($prop['luas_tanah']) ?> m²</span>
                                            <span>LB <?= esc($prop['luas_bangunan']) ?> m²</span>
                                            <span><?= esc($prop['spesifikasi_kamar'] ?: '2 KT / 1 KM') ?></span>
                                        </div>
                                        <a href="<?= site_url('properti/' . $prop['slug']) ?>" class="btn btn-sm btn-primary w-100 rounded-pill mt-3 py-1" style="font-size: 0.78rem;">
                                            Detail Unit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Related Articles -->
                <?php if (!empty($relatedArticles)): ?>
                <div class="pt-4 border-top">
                    <h4 class="fw-bold text-dark mb-4">Artikel Terkait Lainnya</h4>
                    <div class="row g-3">
                        <?php foreach ($relatedArticles as $rel): ?>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100 bg-white">
                                    <a href="<?= site_url('blog/' . $rel['slug']) ?>">
                                        <img src="<?= esc($rel['featured_image'] ?: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=400&auto=format&fit=crop&q=80') ?>" alt="<?= esc($rel['title']) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" class="w-100 object-fit-cover" style="height: 140px;">
                                    </a>
                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <span class="badge bg-light text-primary border align-self-start mb-2" style="font-size: 0.68rem;"><?= esc($rel['category']) ?></span>
                                        <h6 class="fw-bold mb-2">
                                            <a href="<?= site_url('blog/' . $rel['slug']) ?>" class="text-dark text-decoration-none">
                                                <?= character_limiter(esc($rel['title']), 45) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted mt-auto" style="font-size: 0.72rem;"><?= date('d M Y', strtotime($rel['created_at'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="text-center mt-5 pt-3">
                    <a href="<?= site_url('blog') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Semua Artikel
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>

<?= $this->endSection() ?>
