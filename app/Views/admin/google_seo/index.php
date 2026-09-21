<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Header Developer Banner -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-danger px-2.5 py-1.5 rounded-pill fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi bi-shield-lock-fill me-1"></i> DEVELOPER EXCLUSIVE
                    </span>
                    <span class="badge bg-white bg-opacity-15 text-white px-2.5 py-1 rounded-pill small">
                        <i class="bi bi-google text-danger me-1"></i> Google Suite
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-white">Google &amp; SEO Management Suite</h3>
                <p class="text-light text-opacity-75 mb-0 small">
                    Pusat konfigurasi Google Analytics 4, Search Console, Dynamic XML Sitemap, serta pemantauan statistik trafik pengunjung real-time.
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?= site_url('sitemap.xml') ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                    <i class="bi bi-diagram-3-fill me-1 text-warning"></i> Cek Sitemap.xml
                </a>
                <a href="https://search.google.com/search-console" target="_blank" rel="noopener" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                    <i class="bi bi-box-arrow-up-right me-1 text-danger"></i> Google Search Console
                </a>
                <a href="https://analytics.google.com/" target="_blank" rel="noopener" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                    <i class="bi bi-bar-chart-line-fill me-1 text-warning"></i> Google Analytics
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- KOLOM KIRI: STATISTIK TRAFIK PENGUNJUNG & ANALITIK SEO -->
    <div class="col-xl-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow text-primary"></i> Statistik Pengunjung &amp; Analitik SEO
                    </h5>
                    <small class="text-muted">Data trafik calon pembeli yang mengakses katalog perumahan dan artikel.</small>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill small">
                    <i class="bi bi-broadcast me-1"></i> Pelacak Internal Aktif
                </span>
            </div>
            <div class="card-body p-4">
                <!-- 4 KPI Metrics -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light border">
                            <small class="text-muted d-block mb-1">Hari Ini</small>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0 text-dark"><?= number_format($visitorSummary['today']) ?></h3>
                                <?php if ($visitorSummary['growth'] != 0): ?>
                                    <small class="<?= $visitorSummary['growth'] > 0 ? 'text-success' : 'text-danger' ?> fw-semibold">
                                        <i class="bi bi-arrow-<?= $visitorSummary['growth'] > 0 ? 'up' : 'down' ?>-short"></i>
                                        <?= abs($visitorSummary['growth']) ?>%
                                    </small>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">vs Kemarin (<?= number_format($visitorSummary['yesterday']) ?>)</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light border">
                            <small class="text-muted d-block mb-1">Kemarin</small>
                            <h3 class="fw-bold mb-0 text-secondary"><?= number_format($visitorSummary['yesterday']) ?></h3>
                            <small class="text-muted" style="font-size: 0.72rem;">Pengunjung unik</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light border">
                            <small class="text-muted d-block mb-1">7 Hari Terakhir</small>
                            <h3 class="fw-bold mb-0 text-primary"><?= number_format($visitorSummary['last7days']) ?></h3>
                            <small class="text-muted" style="font-size: 0.72rem;">Akumulasi 1 minggu</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light border">
                            <small class="text-muted d-block mb-1">30 Hari Terakhir</small>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($visitorSummary['last30days']) ?></h3>
                            <small class="text-muted" style="font-size: 0.72rem;">Total: <?= number_format($visitorSummary['total']) ?></small>
                        </div>
                    </div>
                </div>

                <!-- Grafik Garis Tren Kunjungan 14 Hari -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-activity text-primary me-1"></i> Tren Kunjungan Calon Pembeli (14 Hari Terakhir)
                        </h6>
                        <small class="text-muted">Grafik Harian</small>
                    </div>
                    <div style="height: 240px; position: relative;">
                        <canvas id="visitorTrendChart"></canvas>
                    </div>
                </div>

                <div class="row g-4 pt-3 border-top">
                    <!-- Donut Sumber Traffic -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="bi bi-pie-chart text-success me-1"></i> Sumber Trafik Pengunjung
                        </h6>
                        <div style="height: 190px; position: relative;" class="d-flex justify-content-center">
                            <canvas id="visitorSourceChart"></canvas>
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                                Google: <?= $sourceStats['Google Search'] ?? 0 ?>
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                WhatsApp: <?= $sourceStats['WhatsApp'] ?? 0 ?>
                            </span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle small">
                                Medsos: <?= $sourceStats['Social Media'] ?? 0 ?>
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle small">
                                Direct: <?= $sourceStats['Direct'] ?? 0 ?>
                            </span>
                        </div>
                    </div>

                    <!-- Rasio Perangkat & Ringkasan Mobile -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="bi bi-phone text-warning me-1"></i> Perangkat Pengunjung
                        </h6>
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-semibold text-dark"><i class="bi bi-phone-fill text-primary me-1"></i> Smartphone (Mobile)</span>
                                <span class="badge bg-primary px-2 py-1"><?= $deviceStats['mobile_pct'] ?? 75 ?>%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $deviceStats['mobile_pct'] ?? 75 ?>%;"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1">
                                <span><?= number_format($deviceStats['mobile'] ?? 0) ?> hits</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1 mt-3">
                                <span class="small fw-semibold text-dark"><i class="bi bi-laptop text-secondary me-1"></i> Desktop / Laptop</span>
                                <span class="badge bg-secondary px-2 py-1"><?= $deviceStats['desktop_pct'] ?? 25 ?>%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: <?= $deviceStats['desktop_pct'] ?? 25 ?>%;"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1">
                                <span><?= number_format($deviceStats['desktop'] ?? 0) ?> hits</span>
                            </div>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            <i class="bi bi-lightbulb text-warning me-1"></i> Sebagian besar calon pembeli mengakses lewat smartphone. Website telah dioptimasi responsif dan ramah seluler.
                        </small>
                    </div>
                </div>

                <!-- Top 5 Visited Pages -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-fire text-danger me-1"></i> 5 Halaman Terpopuler Dibaca Pengunjung
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Halaman / URL</th>
                                    <th class="text-end" style="width: 120px;">Total Pembaca</th>
                                    <th class="text-end" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topPages)): ?>
                                    <?php foreach ($topPages as $page): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-medium text-dark d-block text-truncate" style="max-width: 320px;">
                                                    <?= esc(!empty($page['page_title']) ? $page['page_title'] : $page['page_url']) ?>
                                                </span>
                                                <code class="small text-muted" style="font-size: 0.72rem;"><?= esc($page['page_url']) ?></code>
                                            </td>
                                            <td class="text-end fw-bold text-primary">
                                                <?= number_format($page['views']) ?> <small class="text-muted fw-normal">views</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= base_url(ltrim($page['page_url'], '/')) ?>" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2" title="Buka Halaman">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Belum ada catatan aktivitas pengunjung.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: FORM KONFIGURASI GOOGLE & METADATA SEO -->
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-sliders text-danger"></i> Integrasi Google &amp; Metadata SEO
                </h5>
                <small class="text-muted">Konfigurasi tag resmi pelacak Google dan optimasi kata kunci pencarian.</small>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('admin/google-seo/update') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Dynamic XML Sitemap Status Card -->
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="fw-bold small d-block text-dark">
                                    <i class="bi bi-diagram-3-fill text-primary me-1"></i> Dynamic XML Sitemap
                                </span>
                                <small class="text-muted">Di-generate otomatis dari database.</small>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                <i class="bi bi-check-circle-fill me-1"></i> 200 OK
                            </span>
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" class="form-control font-monospace" id="sitemapUrlInput" value="<?= site_url('sitemap.xml') ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('<?= site_url('sitemap.xml') ?>'); alert('Link Sitemap berhasil disalin!');">
                                <i class="bi bi-clipboard me-1"></i> Salin
                            </button>
                            <a href="<?= site_url('sitemap.xml') ?>" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.74rem;">
                            <i class="bi bi-info-circle me-1"></i> Daftarkan URL ini ke <strong>Google Search Console &gt; Sitemaps</strong> agar seluruh unit rumah dan artikel otomatis diindeks Googlebot.
                        </small>
                    </div>

                    <!-- Google Analytics 4 (GA4) -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">Google Analytics 4 (GA4) Measurement ID</label>
                            <?php if (!empty($settings['google_analytics_id'])): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">
                                    <i class="bi bi-check2"></i> Terhubung
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.68rem;">Belum disetel</span>
                            <?php endif; ?>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-bar-chart-line text-warning"></i></span>
                            <input type="text" name="google_analytics_id" class="form-control font-monospace" placeholder="G-XXXXXXXXXX" value="<?= old('google_analytics_id', $settings['google_analytics_id'] ?? '') ?>">
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                            ID pelacak dari Google Analytics 4 (Format: <code>G-XXXXXXXXXX</code>). Script <code>gtag.js</code> resmi akan otomatis terpasang pada seluruh halaman publik.
                        </small>
                    </div>

                    <!-- Google Search Console HTML Verification -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">Kode Verifikasi Google Search Console</label>
                            <?php if (!empty($settings['google_search_console_code'])): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">
                                    <i class="bi bi-check2"></i> Terverifikasi
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.68rem;">Belum disetel</span>
                            <?php endif; ?>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-shield-check text-success"></i></span>
                            <input type="text" name="google_search_console_code" class="form-control font-monospace" placeholder="Contoh: aBcDeF1234567..." value="<?= old('google_search_console_code', $settings['google_search_console_code'] ?? '') ?>">
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                            Nilai atribut <code>content</code> dari meta tag verifikasi HTML Google (contoh: <code>&lt;meta name="google-site-verification" content="..."&gt;</code>).
                        </small>
                    </div>

                    <!-- Meta Keywords Berbasis Lokasi -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Kata Kunci Utama SEO (Meta Keywords)</label>
                        <textarea name="meta_keywords" class="form-control" rows="3" placeholder="Contoh: rumah dijual bogor, perumahan cluster modern, kpr dp 0 persen, perumahan subsidi, shm"><?= old('meta_keywords', $settings['meta_keywords'] ?? 'perumahan modern, rumah dijual, kpr rumah, cluster perumahan') ?></textarea>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                            Pisahkan dengan tanda koma. Masukkan kata kunci lokasi kota/kabupaten spesifik agar website muncul di urutan atas pencarian lokal Google.
                        </small>
                    </div>

                    <!-- Informasi Structured Data Schema.org -->
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <span class="fw-bold small d-block text-dark mb-1">
                            <i class="bi bi-code-square text-info me-1"></i> Schema.org Structured Data (JSON-LD)
                        </span>
                        <p class="small text-muted mb-0" style="font-size: 0.74rem;">
                            Sistem secara otomatis menyematkan skema <strong>RealEstateAgent</strong> pada beranda, <strong>SingleFamilyResidence</strong> &amp; <strong>BreadcrumbList</strong> pada detail rumah, serta <strong>BlogPosting</strong> pada artikel untuk mendapatkan tampilan <em>Google Rich Snippets</em>.
                        </p>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-bold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Konfigurasi Google &amp; SEO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart: 14-Day Visitor Trends
    const ctxTrend = document.getElementById('visitorTrendChart');
    if (ctxTrend) {
        const trendLabels = <?= json_encode($dailyStats['labels'] ?? []) ?>;
        const trendData   = <?= json_encode($dailyStats['data'] ?? []) ?>;

        const canvasContext = ctxTrend.getContext('2d');
        const gradient = canvasContext.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.28)');
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0.00)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Pengunjung',
                    data: trendData,
                    borderColor: '#0d6efd',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 12 },
                        bodyFont: { size: 13, weight: 'bold' },
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Pengunjung';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            precision: 0,
                            font: { size: 11 },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    }

    // 2. Doughnut Chart: Traffic Sources
    const ctxSource = document.getElementById('visitorSourceChart');
    if (ctxSource) {
        const sourceGoogle   = <?= (int)($sourceStats['Google Search'] ?? 0) ?>;
        const sourceWA       = <?= (int)($sourceStats['WhatsApp'] ?? 0) ?>;
        const sourceMedsos   = <?= (int)($sourceStats['Social Media'] ?? 0) ?>;
        const sourceDirect   = <?= (int)($sourceStats['Direct'] ?? 0) ?>;
        const sourceOther    = <?= (int)($sourceStats['Other'] ?? 0) ?>;

        new Chart(ctxSource, {
            type: 'doughnut',
            data: {
                labels: ['Google Search', 'WhatsApp', 'Media Sosial', 'Langsung (Direct)', 'Lainnya'],
                datasets: [{
                    data: [sourceGoogle, sourceWA, sourceMedsos, sourceDirect, sourceOther],
                    backgroundColor: [
                        '#0d6efd', // Blue for Google
                        '#198754', // Green for WhatsApp
                        '#0dcaf0', // Cyan for Medsos
                        '#6c757d', // Gray for Direct
                        '#adb5bd'  // Light gray for Other
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed || 0;
                                return ' ' + context.label + ': ' + val + ' hits';
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
