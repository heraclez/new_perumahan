<?php
$primary = $settings['primary_color'] ?? '#1e3a8a';
$secondary = $settings['secondary_color'] ?? '#0d9488';

// Function to convert hex to rgb
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) == 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "$r, $g, $b";
}

$primaryRgb = hexToRgb($primary);
$secondaryRgb = hexToRgb($secondary);
$companyName = $settings['company_name'] ?? 'Grand Harmoni Residence';
$companyTagline = $settings['company_tagline'] ?? 'Hunian Modern, Asri & Nyaman';
$companyLogo = $settings['company_logo'] ?? '';
$companyPhone = $settings['company_phone'] ?? '021-5558989';
$companyWa = $settings['company_whatsapp'] ?? '6281234567890';
$companyEmail = $settings['company_email'] ?? 'marketing@perumahan.com';
$companyAddress = $settings['company_address'] ?? 'Jakarta Barat, Indonesia';
$vaName = $settings['va_name'] ?? 'Sarah - Konsultan Properti';
$vaPhone = $settings['va_phone'] ?? $companyWa;
$siteThemePreset = $_GET['theme'] ?? ($settings['site_theme_preset'] ?? 'modern-residential');
if ($siteThemePreset === 'industrialist') $siteThemePreset = 'modern-residential';
if ($siteThemePreset === 'brutalist') $siteThemePreset = 'soft-brutalist';
$headingFont = $settings['heading_font'] ?? 'default';
$bodyFont = $settings['body_font'] ?? 'default';
$gradientFrom = $settings['gradient_from'] ?? $primary;
$gradientTo = $settings['gradient_to'] ?? $secondary;
$gradientAngle = $settings['gradient_angle'] ?? '135deg';

$fontMap = [
    'plus-jakarta-sans' => "'Plus Jakarta Sans', sans-serif",
    'space-grotesk'     => "'Space Grotesk', sans-serif",
    'onest'              => "'Onest', sans-serif",
    'inter'              => "'Inter', sans-serif",
    'playfair-display'   => "'Playfair Display', Georgia, serif",
];
$isAdminLoggedIn = session()->get('is_admin_logged_in') ?? false;
?>
<!DOCTYPE html>
<html lang="id" data-theme-preset="<?= esc($siteThemePreset) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? $companyName) ?></title>
    <meta name="description" content="<?= esc($meta_description ?? $companyTagline) ?>">
    <?php if (!empty($settings['meta_keywords'])): ?>
    <meta name="keywords" content="<?= esc($settings['meta_keywords']) ?>">
    <?php endif; ?>
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="<?= current_url() ?>">

    <?php if (!empty($companyLogo) && file_exists(FCPATH . 'uploads/settings/' . $companyLogo)): ?>
        <link rel="icon" href="<?= base_url('uploads/settings/' . esc($companyLogo)) ?>">
    <?php endif; ?>

    <!-- Google Search Console Verification -->
    <?php if (!empty($settings['google_search_console_code'])): ?>
    <meta name="google-site-verification" content="<?= esc($settings['google_search_console_code']) ?>">
    <?php endif; ?>

    <!-- Google Analytics 4 (GA4) -->
    <?php if (!empty($settings['google_analytics_id'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($settings['google_analytics_id']) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= esc($settings['google_analytics_id']) ?>');
    </script>
    <?php endif; ?>

    <!-- Open Graph Dynamic Meta -->
    <?= $this->renderSection('og_tags') ?>
    <?php if (!$this->renderSection('og_tags')): ?>
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?= esc($title ?? $companyName) ?>">
        <meta property="og:description" content="<?= esc($meta_description ?? $companyTagline) ?>">
        <meta property="og:url" content="<?= base_url(uri_string()) ?>">
        <meta property="og:site_name" content="<?= esc($companyName) ?>">
        <?php if (!empty($companyLogo)): ?>
        <meta property="og:image" content="<?= base_url('uploads/settings/' . esc($companyLogo)) ?>">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <?php endif; ?>
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?= esc($title ?? $companyName) ?>">
        <meta name="twitter:description" content="<?= esc($meta_description ?? $companyTagline) ?>">
    <?php endif; ?>

    <!-- Schema.org Structured Data (WebSite Sitelinks Searchbox) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": <?= json_encode($companyName) ?>,
      "url": <?= json_encode(site_url('/')) ?>,
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": <?= json_encode(site_url('properti') . '?keyword={search_term_string}') ?>
        },
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    <!-- Schema.org Structured Data (RealEstateAgent) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "RealEstateAgent",
      "name": <?= json_encode($companyName) ?>,
      "url": <?= json_encode(site_url('/')) ?>,
      <?php if (!empty($companyLogo)): ?>
      "logo": <?= json_encode(base_url('uploads/settings/' . $companyLogo)) ?>,
      "image": <?= json_encode(base_url('uploads/settings/' . $companyLogo)) ?>,
      <?php endif; ?>
      "description": <?= json_encode($companyTagline) ?>,
      "telephone": <?= json_encode($settings['company_phone'] ?? $settings['company_whatsapp'] ?? '') ?>,
      "address": {
        "@type": "PostalAddress",
        "streetAddress": <?= json_encode($settings['company_address'] ?? '') ?>,
        "addressCountry": "ID"
      },
      "priceRange": "Rp 300 Juta - Rp 2 Miliar"
    }
    </script>
    <?= $this->renderSection('schema_json_ld') ?>

    <!-- Fonts & Icons (Optimized: Plus Jakarta Sans & Space Grotesk) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons (Local Vendor with CDN Fallback) -->
    <?php if (file_exists(FCPATH . 'assets/vendor/bootstrap-icons/bootstrap-icons.min.css')): ?>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <?php else: ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php endif; ?>
    
    <!-- Bootstrap 5 CSS (Local Vendor with CDN Fallback) -->
    <?php if (file_exists(FCPATH . 'assets/vendor/bootstrap/css/bootstrap.min.css')): ?>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <?php else: ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>

    <!-- Dynamic Theme Color, Gradient & Typography Override -->
    <style>
        :root {
            --bs-primary: <?= $primary ?>;
            --bs-primary-rgb: <?= $primaryRgb ?>;
            --bs-secondary: <?= $secondary ?>;
            --bs-secondary-rgb: <?= $secondaryRgb ?>;
            --theme-primary: <?= $primary ?>;
            --theme-secondary: <?= $secondary ?>;
            --theme-gradient: linear-gradient(<?= esc($gradientAngle) ?>, <?= esc($gradientFrom) ?>, <?= esc($gradientTo) ?>);
            <?php if (!empty($headingFont) && $headingFont !== 'default' && isset($fontMap[$headingFont])): ?>
            --heading-font: <?= $fontMap[$headingFont] ?>;
            <?php endif; ?>
            <?php if (!empty($bodyFont) && $bodyFont !== 'default' && isset($fontMap[$bodyFont])): ?>
            --body-font: <?= $fontMap[$bodyFont] ?>;
            <?php endif; ?>
        }

        <?php if (!empty($headingFont) && $headingFont !== 'default' && isset($fontMap[$headingFont])): ?>
        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: var(--heading-font) !important;
        }
        <?php endif; ?>

        <?php if (!empty($bodyFont) && $bodyFont !== 'default' && isset($fontMap[$bodyFont])): ?>
        body, p, li, .lead {
            font-family: var(--body-font) !important;
        }
        <?php endif; ?>

        body {
            color: #334155;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .bg-primary { background-color: var(--bs-primary) !important; }
        .text-primary { color: var(--bs-primary) !important; }
        .border-primary { border-color: var(--bs-primary) !important; }
        
        .btn-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #ffffff !important;
            font-weight: 600;
            transition: all 0.25s ease;
        }
        .btn-primary:hover, .btn-primary:focus {
            filter: brightness(0.88);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.35);
        }

        .btn-secondary {
            background-color: var(--bs-secondary) !important;
            border-color: var(--bs-secondary) !important;
            color: #ffffff !important;
            font-weight: 600;
        }
        .btn-secondary:hover {
            filter: brightness(0.9);
        }

        .btn-outline-primary {
            color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background-color: var(--bs-primary) !important;
            color: #ffffff !important;
        }

        .badge-primary-soft {
            background-color: rgba(var(--bs-primary-rgb), 0.12);
            color: var(--bs-primary);
            font-weight: 600;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--bs-primary) !important;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-custom:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        /* Floating Virtual Assistant */
        .va-floating-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1050;
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-secondary));
            color: #fff;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(var(--bs-primary-rgb), 0.4);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 3px solid #ffffff;
        }
        .va-floating-btn:hover {
            transform: scale(1.1) rotate(5deg);
        }
        .va-pulse-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 14px;
            height: 14px;
            background-color: #22c55e;
            border: 2px solid #ffffff;
            border-radius: 50%;
            animation: pulse-ring 1.8s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        .va-chat-box {
            position: fixed;
            bottom: 96px;
            right: 24px;
            width: 360px;
            max-width: calc(100vw - 48px);
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.18);
            z-index: 1050;
            display: none;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .va-chat-header {
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-secondary));
            color: #fff;
            padding: 16px;
            position: relative;
        }

        .va-chat-body {
            height: 330px;
            overflow-y: auto;
            padding: 16px;
            background-color: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .va-bubble-bot {
            background: #ffffff;
            color: #1e293b;
            padding: 10px 14px;
            border-radius: 14px 14px 14px 2px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            font-size: 0.88rem;
            max-width: 85%;
            align-self: flex-start;
        }

        .va-bubble-user {
            background: var(--bs-primary);
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 14px 14px 2px 14px;
            font-size: 0.88rem;
            max-width: 85%;
            align-self: flex-end;
        }

        .va-quick-option {
            background: #ffffff;
            border: 1px solid rgba(var(--bs-primary-rgb), 0.25);
            color: var(--bs-primary);
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .va-quick-option:hover {
            background: var(--bs-primary);
            color: #ffffff;
        }

        /* Mobile adjustments for Floating Widgets & Bottom Bar */
        @media (max-width: 767.98px) {
            .va-floating-btn {
                bottom: 82px;
                right: 18px;
                width: 54px;
                height: 54px;
            }
            .va-chat-box {
                bottom: 144px;
                right: 12px;
                max-width: calc(100vw - 24px);
            }
            .has-bottom-bar {
                padding-bottom: 74px !important;
            }
        }
    </style>
    
    <!-- Design Tokens & Theme Presets (Industrialist vs Brutalist) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/design-tokens.css') ?>?v=<?= file_exists(FCPATH . 'assets/css/design-tokens.css') ? filemtime(FCPATH . 'assets/css/design-tokens.css') : time() ?>">

    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100">

    <?php if ($isAdminLoggedIn): ?>
    <!-- Top Admin Bar / Editor Bar for Public Site -->
    <div id="adminPublicBar" class="bg-dark text-white py-1 px-3 border-bottom border-warning d-flex align-items-center justify-content-between position-sticky top-0 shadow-sm" style="font-family: system-ui, -apple-system, sans-serif; font-size: 0.82rem; z-index: 1070;">
        <div class="d-flex align-items-center gap-2 gap-md-3 flex-wrap">
            <span class="badge bg-warning text-dark fw-bold d-inline-flex align-items-center gap-1">
                <i class="bi bi-pencil-square"></i> Mode Editor Publik Aktif
            </span>
            <span class="text-white-50 d-none d-sm-inline">
                Tema: <strong class="text-white text-capitalize"><?= esc(str_replace('-', ' ', $siteThemePreset)) ?></strong>
            </span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle py-0 px-2 fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.78rem; border-color: rgba(255,255,255,0.4);">
                    <i class="bi bi-list-nested me-1"></i> Edit Konten Section
                </button>
                <ul class="dropdown-menu shadow-lg border-0 rounded-3 mt-1" style="font-size: 0.82rem;">
                    <li><h6 class="dropdown-header small text-uppercase">Pilih Bagian untuk Diedit:</h6></li>
                    <li><a class="dropdown-item py-1.5" href="<?= site_url('admin/sections/edit/hero') ?>"><i class="bi bi-star-fill text-warning me-2"></i> Hero Banner &amp; Video</a></li>
                    <li><a class="dropdown-item py-1.5" href="<?= site_url('admin/sections/edit/properties') ?>"><i class="bi bi-houses-fill text-primary me-2"></i> Properti Pilihan</a></li>
                    <li><a class="dropdown-item py-1.5" href="<?= site_url('admin/sections/edit/about') ?>"><i class="bi bi-info-circle-fill text-info me-2"></i> Tentang Perumahan</a></li>
                    <li><a class="dropdown-item py-1.5" href="<?= site_url('admin/sections/edit/features') ?>"><i class="bi bi-check-circle-fill text-success me-2"></i> Fasilitas &amp; Keunggulan</a></li>
                    <li><a class="dropdown-item py-1.5" href="<?= site_url('admin/sections/edit/gallery') ?>"><i class="bi bi-images text-danger me-2"></i> Galeri Kawasan</a></li>
                    <li><a class="dropdown-item py-1.5" href="<?= site_url('admin/sections/edit/siteplan') ?>"><i class="bi bi-map-fill text-success me-2"></i> Master Siteplan</a></li>
                    <li><a class="dropdown-item py-1.5" href="<?= site_url('admin/sections/edit/promo') ?>"><i class="bi bi-fire text-danger me-2"></i> Banner Promo</a></li>
                    <li><a class="dropdown-item py-1.5" href="<?= site_url('admin/sections/edit/cta') ?>"><i class="bi bi-megaphone-fill text-primary me-2"></i> Call to Action / Kontak</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item py-1.5 fw-bold text-primary" href="<?= site_url('admin/sections') ?>"><i class="bi bi-arrows-expand me-2"></i> Editor Urutan Section (Drag &amp; Drop)</a></li>
                </ul>
            </div>
            <a href="<?= site_url('admin/settings') ?>" class="btn btn-sm btn-outline-warning py-0 px-2 fw-semibold d-none d-md-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                <i class="bi bi-palette"></i> Ganti Tema
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-sm btn-primary py-0 px-2 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                <i class="bi bi-speedometer2"></i> Panel Admin
            </a>
            <a href="<?= site_url('admin/logout') ?>" class="btn btn-sm btn-outline-danger py-0 px-2 text-danger" title="Keluar Admin" style="font-size: 0.78rem; border-color: rgba(239, 68, 68, 0.5);">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('/') ?>">
                <?php 
                $navLogoSrc = '';
                if (!empty($companyLogo)) {
                    if (str_starts_with($companyLogo, 'http://') || str_starts_with($companyLogo, 'https://')) {
                        $navLogoSrc = $companyLogo;
                    } elseif (file_exists(FCPATH . 'uploads/settings/' . $companyLogo)) {
                        $navLogoSrc = base_url('uploads/settings/' . $companyLogo);
                    } elseif (file_exists(FCPATH . ltrim($companyLogo, '/'))) {
                        $navLogoSrc = base_url(ltrim($companyLogo, '/'));
                    }
                }
                ?>
                <?php if (!empty($navLogoSrc)): ?>
                    <img src="<?= esc($navLogoSrc) ?>" alt="<?= esc($companyName) ?>" style="max-height: 44px; max-width: 140px; object-fit: contain;">
                <?php else: ?>
                    <i class="bi bi-buildings-fill fs-3 text-primary"></i>
                <?php endif; ?>
                <div>
                    <span class="fs-4 d-block lh-1"><?= esc($companyName) ?></span>
                    <small class="text-muted fw-normal fs-7" style="font-size: 0.75rem;"><?= esc($companyTagline) ?></small>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === '' ? 'active fw-bold text-primary' : '' ?>" href="<?= site_url('/') ?>">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'properti' ? 'active fw-bold text-primary' : '' ?>" href="<?= site_url('properti') ?>">Katalog Rumah</a>
                    </li>

                    <?php 
                    $navSiteplans = $activeSiteplans ?? (new \App\Models\SiteplanModel())->getActiveSiteplans();
                    $navSiteplanCount = count($navSiteplans);
                    ?>

                    <?php if ($navSiteplanCount === 1): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= str_starts_with(uri_string(), 'siteplan') ? 'active fw-bold text-primary' : '' ?>" href="<?= site_url('siteplan/' . $navSiteplans[0]['id']) ?>">
                                <i class="bi bi-map-fill text-primary me-1"></i> Siteplan
                            </a>
                        </li>
                    <?php elseif ($navSiteplanCount > 1): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= str_starts_with(uri_string(), 'siteplan') ? 'active fw-bold text-primary' : '' ?>" href="#" id="navbarSiteplanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-map-fill text-primary me-1"></i> Siteplan
                                <span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 0.68rem; padding: 0.25em 0.55em;"><?= $navSiteplanCount ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" aria-labelledby="navbarSiteplanDropdown" style="min-width: 260px;">
                                <li class="dropdown-header small fw-bold text-uppercase text-muted px-3 py-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pilih Denah Siteplan (<?= $navSiteplanCount ?> Area)</li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <?php foreach ($navSiteplans as $spNav): ?>
                                    <?php 
                                        $isCurNav = (isset($siteplan['id']) && $siteplan['id'] == $spNav['id']) || (uri_string() === 'siteplan/' . $spNav['id']);
                                    ?>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 mb-1 <?= $isCurNav ? 'active bg-primary text-white' : '' ?>" href="<?= site_url('siteplan/' . $spNav['id']) ?>">
                                            <i class="bi bi-geo-alt-fill fs-6 <?= $isCurNav ? 'text-white' : 'text-primary' ?>"></i>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold fs-7"><?= esc($spNav['title']) ?></div>
                                                <?php if (!empty($spNav['description'])): ?>
                                                    <div class="<?= $isCurNav ? 'text-white-50' : 'text-muted' ?>" style="font-size: 0.72rem;"><?= character_limiter(esc($spNav['description']), 30) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'siteplan' ? 'active fw-bold text-primary' : '' ?>" href="<?= site_url('siteplan') ?>">
                                <i class="bi bi-map-fill text-primary me-1"></i> Siteplan
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'kpr-calculator' ? 'active fw-bold text-primary' : '' ?>" href="<?= site_url('kpr-calculator') ?>">Simulasi KPR</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with(uri_string(), 'blog') || str_starts_with(uri_string(), 'artikel') ? 'active fw-bold text-primary' : '' ?>" href="<?= site_url('blog') ?>">Artikel &amp; Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/#kontak') ?>">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary px-3 py-2 rounded-pill d-flex align-items-center gap-2" href="https://wa.me/<?= esc($companyWa) ?>?text=Halo%20<?= urlencode($companyName) ?>,%20saya%20tertarik%20dengan%20properti%20Anda." target="_blank">
                            <i class="bi bi-whatsapp"></i> Hubungi Kami
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow-1">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer id="kontak" class="bg-dark text-white pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <?php if (!empty($companyLogo) && file_exists(FCPATH . 'uploads/settings/' . $companyLogo)): ?>
                            <img src="<?= base_url('uploads/settings/' . esc($companyLogo)) ?>" alt="<?= esc($companyName) ?>" style="max-height: 40px; max-width: 120px; object-fit: contain;" class="bg-white p-1 rounded">
                        <?php else: ?>
                            <i class="bi bi-buildings-fill fs-3 text-white"></i>
                        <?php endif; ?>
                        <h4 class="fw-bold mb-0 text-white"><?= esc($companyName) ?></h4>
                    </div>
                    <p class="text-white-50 mb-3" style="color: #cbd5e1 !important;"><?= esc($settings['company_about'] ?? 'Hunian asri dan modern dengan fasilitas lengkap terintegrasi serta akses transportasi strategis.') ?></p>
                    <div class="d-flex gap-3 align-items-center">
                        <?php if (!empty($companyWa)): ?>
                            <a href="https://wa.me/<?= esc($companyWa) ?>" target="_blank" rel="noopener noreferrer" class="text-white fs-5" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($companyEmail)): ?>
                            <a href="mailto:<?= esc($companyEmail) ?>" class="text-white fs-5" title="Email"><i class="bi bi-envelope"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_instagram'])): ?>
                            <a href="<?= esc($settings['social_instagram']) ?>" target="_blank" rel="noopener noreferrer" class="text-white fs-5" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_facebook'])): ?>
                            <a href="<?= esc($settings['social_facebook']) ?>" target="_blank" rel="noopener noreferrer" class="text-white fs-5" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_tiktok'])): ?>
                            <a href="<?= esc($settings['social_tiktok']) ?>" target="_blank" rel="noopener noreferrer" class="text-white fs-5" title="TikTok"><i class="bi bi-tiktok"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_youtube'])): ?>
                            <a href="<?= esc($settings['social_youtube']) ?>" target="_blank" rel="noopener noreferrer" class="text-white fs-5" title="YouTube"><i class="bi bi-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3 text-white">Navigasi Cepat</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2" style="color: #cbd5e1;">
                        <li><a href="<?= site_url('/') ?>" class="text-white-50 text-decoration-none hover-white">Beranda</a></li>
                        <li><a href="<?= site_url('properti') ?>" class="text-white-50 text-decoration-none hover-white">Katalog Properti</a></li>
                        <li><a href="<?= site_url('kpr-calculator') ?>" class="text-white-50 text-decoration-none hover-white">Kalkulator KPR</a></li>
                        <li><a href="<?= site_url('blog') ?>" class="text-white-50 text-decoration-none hover-white">Artikel &amp; Edukasi</a></li>
                        <li><a href="<?= site_url('brochure/download-global') ?>" class="text-white-50 text-decoration-none hover-white">E-Brochure Digital</a></li>
                        <li><a href="<?= site_url('admin/login') ?>" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-lock-fill me-1"></i> Admin Portal</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3 text-white">Kantor Pemasaran</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2" style="color: #cbd5e1;">
                        <li class="d-flex gap-2">
                            <i class="bi bi-geo-alt-fill text-warning"></i>
                            <span class="text-white-50"><?= esc($companyAddress) ?></span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-telephone-fill text-warning"></i>
                            <span class="text-white-50"><?= esc($companyPhone) ?></span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-whatsapp text-warning"></i>
                            <span class="text-white-50">+<?= esc($companyWa) ?></span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-envelope-fill text-warning"></i>
                            <span class="text-white-50"><?= esc($companyEmail) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4 opacity-25">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center text-white-50 small" style="color: #94a3b8 !important;">
                <p class="mb-0">&copy; <?= date('Y') ?> <?= esc($companyName) ?>. All rights reserved.</p>
                <p class="mb-0">Designed for Seamless Property Experience.</p>
            </div>
        </div>
    </footer>

    <!-- PROMO POP-UP MODAL (Dynamic from Settings & jQuery sessionStorage) -->
    <?php if (($settings['promo_modal_active'] ?? '0') === '1'): ?>
        <div class="modal fade" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 18px;">
                    <div class="modal-body p-0 position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3 bg-white p-2 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close" id="btnClosePromo"></button>
                        <div class="row g-0">
                            <div class="col-md-6">
                                <?php 
                                $promoImg = $settings['promo_modal_image'] ?? '';
                                if (empty($promoImg)) {
                                    $promoImg = 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&auto=format&fit=crop&q=60';
                                }
                                ?>
                                <img src="<?= esc($promoImg) ?>" alt="Promo Spesial - <?= esc($companyName) ?>" loading="lazy" decoding="async" class="w-100 h-100 object-fit-cover" style="min-height: 280px; max-height: 380px;">
                            </div>
                            <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                                <span class="badge bg-danger align-self-start mb-2 px-3 py-1 rounded-pill">PENERIMA TERBATAS</span>
                                <h4 class="fw-bold text-dark mb-2" id="promoModalLabel"><?= esc($settings['promo_modal_title'] ?? 'Promo Spesial Bulan Ini!') ?></h4>
                                <p class="text-muted small mb-4"><?= nl2br(esc($settings['promo_modal_desc'] ?? 'Dapatkan diskon DP dan bunga subsidi khusus untuk pendaftaran hari ini.')) ?></p>
                                <div class="d-grid gap-2">
                                    <a href="https://wa.me/<?= esc($companyWa) ?>?text=Halo%20<?= urlencode($companyName) ?>,%20saya%20ingin%20mengklaim%20promo%20spesial%20<?= urlencode($settings['promo_modal_title'] ?? '') ?>" target="_blank" class="btn btn-primary py-2 rounded-pill d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-whatsapp"></i> <?= esc($settings['promo_modal_btn_text'] ?? 'Klaim Promo via WhatsApp') ?>
                                    </a>
                                    <button type="button" class="btn btn-light py-2 rounded-pill text-muted" data-bs-dismiss="modal">Nanti Saja</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- UNIT PROMO POP-UP MODAL (Interactive for Checked Promo Properties) -->
    <div class="modal fade" id="unitPromoModal" tabindex="-1" aria-labelledby="unitPromoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-0 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3 bg-white p-2 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="row g-0">
                        <div class="col-md-5 position-relative bg-light">
                            <img src="" id="modalPromoUnitImg" alt="Unit Promo - <?= esc($companyName) ?>" loading="lazy" decoding="async" class="w-100 h-100 object-fit-cover" style="min-height: 280px; max-height: 400px;">
                            <span class="position-absolute top-0 start-0 m-3 badge bg-danger text-white rounded-pill px-3 py-2 shadow-sm">
                                <i class="bi bi-fire text-warning me-1"></i> PROMO SPESIAL
                            </span>
                        </div>
                        <div class="col-md-7 p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill small fw-bold shadow-sm">
                                        <i class="bi bi-tag-fill me-1 text-danger"></i> Penawaran Eksklusif
                                    </span>
                                    <span class="fw-extrabold text-primary fs-5" id="modalPromoUnitPrice"></span>
                                </div>
                                <h4 class="fw-bold text-dark mb-1" id="modalPromoUnitTitle"></h4>
                                <h6 class="fw-bold text-danger mb-3" id="modalPromoTitleText"></h6>
                                
                                <div class="bg-light p-3 rounded-3 mb-3 border">
                                    <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-gift-fill text-danger me-1"></i> Keuntungan & Bonus Promo:</h6>
                                    <div class="text-muted small" id="modalPromoDescText" style="line-height: 1.6;"></div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2 mt-2">
                                <a href="#" target="_blank" class="btn btn-success py-2 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" id="btnClaimUnitPromoWa">
                                    <i class="bi bi-whatsapp fs-5"></i> Klaim Promo Unit Ini via WhatsApp
                                </a>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm py-2 rounded-pill flex-grow-1" id="btnViewUnitDetail">
                                        <i class="bi bi-house-door me-1"></i> Lihat Spesifikasi Rumah
                                    </a>
                                    <button type="button" class="btn btn-light btn-sm py-2 rounded-pill px-3 text-muted" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VIRTUAL ASSISTANT FLOATING CHAT WIDGET -->
    <div class="va-floating-btn" id="btnToggleVA" title="Chat dengan <?= esc($vaName) ?>">
        <div class="va-pulse-dot"></div>
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </div>

    <div class="va-chat-box" id="vaChatBox">
        <div class="va-chat-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                    <i class="bi bi-robot fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-white fs-6"><?= esc($vaName) ?></h6>
                    <small class="text-white-50 fs-7" style="font-size: 0.72rem;"><i class="bi bi-circle-fill text-success" style="font-size: 0.55rem;"></i> Online | Siap Menjawab</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <span class="badge bg-white text-primary border px-2 py-1 rounded-pill shadow-sm" id="vaUserHeaderBadge" style="display: none; font-size: 0.7rem;">
                    <i class="bi bi-person-fill me-1"></i><span id="vaUserHeaderName"></span>
                </span>
                <button type="button" class="btn btn-sm text-white-50 p-1" id="vaResetChat" title="Reset Percakapan"><i class="bi bi-arrow-counterclockwise fs-6"></i></button>
                <button type="button" class="btn-close btn-close-white ms-1" id="btnCloseVA"></button>
            </div>
        </div>

        <!-- Chat Conversation Messages Body -->
        <div class="va-chat-body" id="vaChatBody">
            <!-- Dynamic Content initialized by JavaScript -->
        </div>

        <!-- Interactive Typing Input Area -->
        <div class="p-2 bg-white border-top">
            <form id="vaChatForm" class="d-flex gap-2 align-items-center">
                <input type="text" id="vaUserInput" class="form-control form-control-sm rounded-pill px-3" placeholder="Ketik pertanyaan Anda di sini..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;" title="Kirim Pesan">
                    <i class="bi bi-send-fill" style="font-size: 0.85rem;"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- GLOBAL TOAST UI NOTIFICATION -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
        <div id="globalAppToast" class="toast align-items-center text-bg-dark border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2" id="globalToastBody">
                    <i class="bi bi-info-circle-fill text-info"></i> <span>Pemberitahuan</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Scripts (Local Vendor with CDN Fallback) -->
    <?php if (file_exists(FCPATH . 'assets/vendor/jquery/jquery.min.js')): ?>
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <?php else: ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <?php endif; ?>

    <?php if (file_exists(FCPATH . 'assets/vendor/bootstrap/js/bootstrap.bundle.min.js')): ?>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <?php else: ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php endif; ?>

    <script>
        // Global Toast Notification Helper
        window.showToast = function(msg, icon = 'bi-check-circle-fill', iconColor = 'text-success') {
            var toastEl = document.getElementById('globalAppToast');
            if (toastEl) {
                $('#globalToastBody').html('<i class="bi ' + icon + ' ' + iconColor + ' fs-6"></i> <span>' + msg + '</span>');
                var toast = new bootstrap.Toast(toastEl, { delay: 2500 });
                toast.show();
            }
        };
    </script>

    <script>
        $(document).ready(function() {
            // ==========================================
            // 1. PROMO POP-UP WITH sessionStorage
            // ==========================================
            var promoActive = "<?= $settings['promo_modal_active'] ?? '0' ?>";
            var promoClosed = sessionStorage.getItem('promo_closed_session');

            if (promoActive === '1' && !promoClosed) {
                setTimeout(function() {
                    var promoModalEl = document.getElementById('promoModal');
                    if (promoModalEl) {
                        var promoModal = new bootstrap.Modal(promoModalEl);
                        promoModal.show();
                    }
                }, 1200);
            }

            $('#promoModal').on('hidden.bs.modal', function () {
                sessionStorage.setItem('promo_closed_session', 'true');
            });

            // ==========================================
            // 2. UNIT-SPECIFIC PROMO POP-UP MODAL HANDLER
            // ==========================================
            $(document).on('click', '.btn-unit-promo-trigger', function(e) {
                e.preventDefault();
                var title = $(this).data('title') || '';
                var price = $(this).data('price') || '';
                var promoTitle = $(this).data('promo-title') || 'Promo Spesial Unit';
                var promoDesc = $(this).data('promo-desc') || 'Dapatkan potongan harga, subsidi DP/KPR, dan promo menarik khusus unit ini.';
                var slug = $(this).data('slug') || '';
                var img = $(this).data('img') || '';

                $('#modalPromoUnitTitle').text(title);
                $('#modalPromoUnitPrice').text(price);
                $('#modalPromoTitleText').text(promoTitle);

                // Format bullet perks for promo description
                var descLines = promoDesc ? promoDesc.split('\n') : ['Promo eksklusif terbatas untuk unit ini.'];
                var descHtml = '';
                $.each(descLines, function(i, line) {
                    if (line.trim()) {
                        descHtml += '<div class="d-flex align-items-start gap-2 mb-1.5"><i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1" style="font-size: 0.85rem;"></i><span>' + $('<div>').text(line.trim()).html() + '</span></div>';
                    }
                });
                $('#modalPromoDescText').html(descHtml);
                $('#modalPromoUnitImg').attr('src', img);
                $('#btnViewUnitDetail').attr('href', "<?= site_url('properti') ?>/" + slug);

                var userLead = getUserLead();
                var userGreeting = (userLead && userLead.nama) ? ('Halo <?= esc($companyName) ?>, saya ' + userLead.nama + ', saya tertarik dan ingin klaim promo untuk ' + title + ' (' + promoTitle + '). Mohon info selengkapnya ya.') : ('Halo <?= esc($companyName) ?>, saya tertarik dan ingin klaim promo untuk ' + title + ' (' + promoTitle + '). Mohon info selengkapnya ya.');
                var waUrl = 'https://wa.me/<?= esc($companyWa) ?>?text=' + encodeURIComponent(userGreeting);
                $('#btnClaimUnitPromoWa').attr('href', waUrl);

                var unitModalEl = document.getElementById('unitPromoModal');
                if (unitModalEl) {
                    var modal = new bootstrap.Modal(unitModalEl);
                    modal.show();
                }
            });

            // ==========================================
            // 3. VIRTUAL ASSISTANT FULL INTERACTIVE CONVERSATION & PRE-CHAT LEAD FORM
            // ==========================================
            var vaPhone = "<?= esc($vaPhone) ?>";
            var compName = "<?= esc($companyName) ?>";
            var compAddress = "<?= esc($companyAddress) ?>";
            var vaName = "<?= esc($vaName) ?>";
            var chatHistory = [];

            function escapeHtml(text) {
                if (!text) return '';
                return text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function getUserLead() {
                try {
                    return JSON.parse(localStorage.getItem('va_user_lead') || '{}');
                } catch (e) {
                    return {};
                }
            }

            function updateHeaderUserBadge() {
                var userLead = getUserLead();
                if (userLead.nama && userLead.submitted) {
                    $('#vaUserHeaderName').text(userLead.nama);
                    $('#vaUserHeaderBadge').fadeIn(200);
                } else {
                    $('#vaUserHeaderBadge').hide();
                }
            }

            function initChatBody() {
                var userLead = getUserLead();
                updateHeaderUserBadge();

                // If lead data already exists (either filled or skipped), show standard personalized greeting without form
                if (userLead.submitted || userLead.skipped) {
                    var greetingName = userLead.nama ? ('Kak <strong>' + escapeHtml(userLead.nama) + '</strong>') : '';
                    var welcomeHtml = '<div class="va-bubble-bot shadow-sm">' +
                        'Halo ' + (greetingName ? greetingName + '! ' : '') + 'Selamat datang di <strong>' + compName + '</strong>. 👋<br><br>' +
                        'Saya <strong>' + vaName + '</strong>, konsultan properti resmi. Ada yang bisa saya bantu? Anda bisa mengetik pertanyaan langsung di bawah atau memilih topik cepat:' +
                    '</div>' +
                    '<div class="d-flex flex-wrap gap-1 mt-1" id="vaOptionsContainer">' +
                        '<button class="va-quick-option" data-option="katalog"><i class="bi bi-house-door me-1"></i> Tipe & Harga Rumah</button>' +
                        '<button class="va-quick-option" data-option="siteplan"><i class="bi bi-map-fill me-1"></i> Denah Siteplan</button>' +
                        '<button class="va-quick-option" data-option="kpr"><i class="bi bi-calculator me-1"></i> Simulasi & Syarat KPR</button>' +
                        '<button class="va-quick-option" data-option="promo"><i class="bi bi-tag-fill me-1"></i> Promo & Diskon DP</button>' +
                        '<button class="va-quick-option" data-option="survey"><i class="bi bi-calendar2-check me-1"></i> Jadwal Survey Lokasi</button>' +
                        '<button class="va-quick-option" data-option="legalitas"><i class="bi bi-shield-check me-1"></i> Legalitas & SHM</button>' +
                        '<button class="va-quick-option" data-option="lokasi"><i class="bi bi-geo-alt me-1"></i> Alamat & Akses</button>' +
                    '</div>';
                    $('#vaChatBody').html(welcomeHtml);
                } else {
                    // First time opening: Show interactive 1-time Pre-Chat Lead Capture Form
                    var formHtml = '<div class="va-bubble-bot shadow-sm">' +
                        'Halo! Selamat datang di <strong>' + compName + '</strong>. 👋' +
                        '<div class="mt-2 text-dark" style="font-size: 0.82rem;">' +
                            'Agar kami dapat memberikan rekomendasi tipe hunian, e-brosur, dan promo KPR yang tepat untuk Anda, mohon isi data singkat berikut:' +
                        '</div>' +
                        '<div class="card border-0 bg-white shadow-sm p-3 mt-2 rounded-3 text-start" id="vaLeadFormCard">' +
                            '<form id="vaLeadForm">' +
                                '<div class="mb-2">' +
                                    '<label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.72rem;"><i class="bi bi-person-fill text-primary me-1"></i>Nama Lengkap</label>' +
                                    '<input type="text" id="vaLeadName" class="form-control form-control-sm rounded-pill px-3" placeholder="Contoh: Budi Santoso" required>' +
                                '</div>' +
                                '<div class="mb-2">' +
                                    '<label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.72rem;"><i class="bi bi-whatsapp text-success me-1"></i>Nomor WhatsApp Aktif</label>' +
                                    '<input type="tel" id="vaLeadPhone" class="form-control form-control-sm rounded-pill px-3" placeholder="Contoh: 081234567890" required>' +
                                '</div>' +
                                '<div class="d-grid gap-1 mt-3">' +
                                    '<button type="submit" class="btn btn-primary btn-sm rounded-pill fw-bold py-1 shadow-sm" id="btnSubmitLead">' +
                                        '<i class="bi bi-send-check-fill me-1"></i> Mulai Konsultasi' +
                                    '</button>' +
                                    '<button type="button" class="btn btn-link btn-sm text-muted text-decoration-none py-0 mt-1" id="btnSkipLead" style="font-size: 0.72rem;">' +
                                        'Lewati & Langsung Chat' +
                                    '</button>' +
                                '</div>' +
                            '</form>' +
                        '</div>' +
                    '</div>';
                    $('#vaChatBody').html(formHtml);
                }
            }

            initChatBody();

            $('#btnToggleVA').on('click', function() {
                $('#vaChatBox').fadeToggle(200);
                if ($('#vaChatBox').is(':visible')) {
                    if ($('#vaLeadName').length) {
                        $('#vaLeadName').focus();
                    } else {
                        $('#vaUserInput').focus();
                    }
                }
            });

            $('#btnCloseVA').on('click', function() {
                $('#vaChatBox').fadeOut(200);
            });

            function scrollChatToBottom() {
                var body = $('#vaChatBody');
                body.animate({ scrollTop: body[0].scrollHeight }, 300);
            }

            function appendBotTypingIndicator() {
                var typingId = 'typing_' + Date.now();
                var html = '<div class="va-bubble-bot d-flex align-items-center gap-1 py-2 px-3" id="' + typingId + '">' +
                    '<span class="spinner-grow spinner-grow-sm text-primary" style="width: 8px; height: 8px;"></span>' +
                    '<span class="spinner-grow spinner-grow-sm text-primary" style="width: 8px; height: 8px; animation-delay: 0.15s;"></span>' +
                    '<span class="spinner-grow spinner-grow-sm text-primary" style="width: 8px; height: 8px; animation-delay: 0.3s;"></span>' +
                    '<small class="text-muted ms-1" style="font-size: 0.75rem;">' + compName + ' sedang mengetik...</small>' +
                '</div>';
                $('#vaChatBody').append(html);
                scrollChatToBottom();
                return typingId;
            }

            function appendUserBubble(text) {
                var userLead = getUserLead();
                var displayName = (userLead && userLead.nama) ? escapeHtml(userLead.nama) : 'Anda';
                var bubbleHtml = '<div class="va-bubble-user shadow-sm">' +
                    '<div class="d-flex align-items-center justify-content-end gap-1 mb-1 text-white-50" style="font-size: 0.68rem; font-weight: 600;">' +
                        '<i class="bi bi-person-circle"></i> <span>' + displayName + '</span>' +
                    '</div>' +
                    '<div>' + escapeHtml(text) + '</div>' +
                '</div>';
                $('#vaChatBody').append(bubbleHtml);
                scrollChatToBottom();
            }

            // Enhanced Markdown parser for rich real estate conversation formatting
            function parseMarkdown(text) {
                if (!text) return '';
                var escaped = text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');

                // Bold **text** or __text__
                escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                escaped = escaped.replace(/__(.*?)__/g, '<strong>$1</strong>');

                // Italic *text*
                escaped = escaped.replace(/\*([^\*\n]+)\*/g, '<em>$1</em>');

                // Numbered lists 1. Item
                escaped = escaped.replace(/^(\d+)\.\s+(.+)$/gm, '<div class="ms-1 mb-1"><strong>$1.</strong> $2</div>');

                // Bullet points • or - or *
                escaped = escaped.replace(/^[•\-\*]\s+(.+)$/gm, '<div class="ms-1 mb-1">▪ $1</div>');

                // Double linebreaks to space
                escaped = escaped.replace(/\n\n/g, '<div class="my-2"></div>');
                escaped = escaped.replace(/\n/g, '<br>');

                return escaped;
            }

            // Handle Lead Form Submission (1-Time)
            $(document).on('submit', '#vaLeadForm', function(e) {
                e.preventDefault();
                var name = $('#vaLeadName').val().trim();
                var phone = $('#vaLeadPhone').val().trim();
                if (!name || !phone) return;

                var btn = $('#btnSubmitLead');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                // Store Lead into backend database
                $.ajax({
                    url: "<?= site_url('leads/store') ?>",
                    type: "POST",
                    data: {
                        nama_prospek: name,
                        no_wa: phone,
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                    },
                    dataType: "json",
                    complete: function() {
                        // Persist in localStorage so it never shows again
                        localStorage.setItem('va_user_lead', JSON.stringify({
                            nama: name,
                            phone: phone,
                            submitted: true
                        }));

                        updateHeaderUserBadge();
                        $('#vaLeadFormCard').parent().remove();

                        var successHtml = '<div class="va-bubble-bot shadow-sm">' +
                            'Terima kasih Kak <strong>' + escapeHtml(name) + '</strong>! Data Anda telah tersimpan dengan aman. 🙏<br><br>' +
                            'Saya <strong>' + vaName + '</strong> siap membantu mendampingi Anda menemukan hunian terbaik di <strong>' + compName + '</strong>. Silakan pilih topik cepat di bawah atau ketik pertanyaan langsung:' +
                        '</div>' +
                        '<div class="d-flex flex-wrap gap-1 mt-1" id="vaOptionsContainer">' +
                            '<button class="va-quick-option" data-option="katalog"><i class="bi bi-house-door me-1"></i> Tipe & Harga Rumah</button>' +
                            '<button class="va-quick-option" data-option="kpr"><i class="bi bi-calculator me-1"></i> Simulasi & Syarat KPR</button>' +
                            '<button class="va-quick-option" data-option="promo"><i class="bi bi-tag-fill me-1"></i> Promo & Diskon DP</button>' +
                            '<button class="va-quick-option" data-option="survey"><i class="bi bi-calendar2-check me-1"></i> Jadwal Survey Lokasi</button>' +
                            '<button class="va-quick-option" data-option="legalitas"><i class="bi bi-shield-check me-1"></i> Legalitas & SHM</button>' +
                            '<button class="va-quick-option" data-option="lokasi"><i class="bi bi-geo-alt me-1"></i> Alamat & Akses</button>' +
                        '</div>';

                        $('#vaChatBody').append(successHtml);
                        scrollChatToBottom();
                        $('#vaUserInput').focus();
                    }
                });
            });

            // Handle Skip Lead Form
            $(document).on('click', '#btnSkipLead', function() {
                localStorage.setItem('va_user_lead', JSON.stringify({
                    skipped: true
                }));

                $('#vaLeadFormCard').parent().remove();

                var skipHtml = '<div class="va-bubble-bot shadow-sm">' +
                    'Halo! Saya <strong>' + vaName + '</strong> dari <strong>' + compName + '</strong>. 👋<br><br>' +
                    'Ada yang bisa saya bantu hari ini? Anda bisa mengetik pertanyaan langsung di bawah atau memilih topik cepat berikut:' +
                '</div>' +
                '<div class="d-flex flex-wrap gap-1 mt-1" id="vaOptionsContainer">' +
                    '<button class="va-quick-option" data-option="katalog"><i class="bi bi-house-door me-1"></i> Tipe & Harga Rumah</button>' +
                    '<button class="va-quick-option" data-option="kpr"><i class="bi bi-calculator me-1"></i> Simulasi & Syarat KPR</button>' +
                    '<button class="va-quick-option" data-option="promo"><i class="bi bi-tag-fill me-1"></i> Promo & Diskon DP</button>' +
                    '<button class="va-quick-option" data-option="survey"><i class="bi bi-calendar2-check me-1"></i> Jadwal Survey Lokasi</button>' +
                    '<button class="va-quick-option" data-option="legalitas"><i class="bi bi-shield-check me-1"></i> Legalitas & SHM</button>' +
                    '<button class="va-quick-option" data-option="lokasi"><i class="bi bi-geo-alt me-1"></i> Alamat & Akses</button>' +
                '</div>';

                $('#vaChatBody').append(skipHtml);
                scrollChatToBottom();
                $('#vaUserInput').focus();
            });

            // Intelligent Conversational Response Logic via Gemini AI Backend
            function processUserMessage(rawText) {
                var typingId = appendBotTypingIndicator();
                var userLead = getUserLead();

                $.ajax({
                    url: "<?= site_url('api/chat/send') ?>",
                    type: "POST",
                    data: {
                        message: rawText,
                        user_name: userLead.nama || '',
                        history: JSON.stringify(chatHistory),
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                    },
                    dataType: "json",
                    success: function(res) {
                        $('#' + typingId).remove();

                        var replyText = res.reply || 'Terima kasih atas pertanyaan Anda.';
                        var formattedText = parseMarkdown(replyText);
                        var waGreetingText = userLead.nama ? ('Halo ' + compName + ', saya ' + userLead.nama + ', ' + rawText) : ('Halo ' + compName + ', ' + rawText);
                        var directWaUrl = res.wa_url || ('https://wa.me/' + vaPhone + '?text=' + encodeURIComponent(waGreetingText));

                        // Store in history for multi-turn conversational context
                        chatHistory.push({ role: 'user', text: rawText });
                        chatHistory.push({ role: 'model', text: replyText });
                        if (chatHistory.length > 10) {
                            chatHistory = chatHistory.slice(-10);
                        }

                        var actionButtons = '<div class="mt-2 d-flex flex-wrap gap-2">' +
                            '<a href="' + directWaUrl + '" target="_blank" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm"><i class="bi bi-whatsapp me-1"></i> Konsultasi WhatsApp</a>' +
                            '<a href="<?= site_url('properti') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold"><i class="bi bi-houses me-1"></i> Katalog Properti</a>' +
                        '</div>';

                        var badgeSource = '';
                        if (res.source === 'n8n') {
                            badgeSource = '<div class="d-block text-end mt-1"><small class="badge bg-danger-soft text-danger" style="font-size: 0.65rem;"><i class="bi bi-diagram-3-fill me-1"></i> n8n AI Assistant</small></div>';
                        }

                        var html = '<div class="va-bubble-bot shadow-sm">' + formattedText + badgeSource + actionButtons + '</div>';
                        $('#vaChatBody').append(html);
                        scrollChatToBottom();
                    },
                    error: function() {
                        $('#' + typingId).remove();
                        var waGreetingText = userLead.nama ? ('Halo ' + compName + ', saya ' + userLead.nama + ', ' + rawText) : ('Halo ' + compName + ', ' + rawText);
                        var fallbackWa = 'https://wa.me/' + vaPhone + '?text=' + encodeURIComponent(waGreetingText);
                        var html = '<div class="va-bubble-bot shadow-sm">' +
                            'Halo! Mohon maaf jaringan sedang sibuk. Anda dapat langsung berkonsultasi dengan konsultan pemasaran kami melalui WhatsApp di bawah ini:' +
                            '<div class="mt-2">' +
                                '<a href="' + fallbackWa + '" target="_blank" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-bold"><i class="bi bi-whatsapp me-1"></i> Hubungi WhatsApp Marketing</a>' +
                            '</div>' +
                        '</div>';
                        $('#vaChatBody').append(html);
                        scrollChatToBottom();
                    }
                });
            }

            // Handle Free Text Form Submit
            $('#vaChatForm').on('submit', function(e) {
                e.preventDefault();
                var msg = $('#vaUserInput').val().trim();
                if (!msg) return;

                // If lead form card is still visible when typing directly, dismiss it
                if ($('#vaLeadFormCard').length) {
                    var userLead = getUserLead();
                    if (!userLead.submitted) {
                        localStorage.setItem('va_user_lead', JSON.stringify({ skipped: true }));
                        $('#vaLeadFormCard').parent().remove();
                    }
                }

                // Append user bubble with visitor's name
                appendUserBubble(msg);
                $('#vaUserInput').val('');

                // Process bot response
                processUserMessage(msg);
            });

            // Handle Quick Options
            $(document).on('click', '.va-quick-option', function() {
                var text = $(this).text().trim();

                appendUserBubble(text);
                processUserMessage(text);
            });

            // Reset Chat
            $('#vaResetChat').on('click', function() {
                chatHistory = [];
                initChatBody();
                $('#vaUserInput').focus();
            });
        });
    </script>

    <script>
        // Apply theme preset configured by Administrator in Admin Panel
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const queryTheme = urlParams.get('theme');
                const defaultTheme = "<?= esc($siteThemePreset) ?>" || 'modern-residential';
                const activeTheme = queryTheme || defaultTheme;

                document.documentElement.setAttribute('data-theme-preset', activeTheme);
                if (document.body) {
                    document.body.setAttribute('data-theme-preset', activeTheme);
                }
            } catch(e) {
                console.error('Theme init error:', e);
            }
        });
    </script>

    <!-- ========================================== -->
    <!-- GLOBAL MODAL LEAD MAGNET E-KATALOG LENGKAP -->
    <!-- ========================================== -->
    <div class="modal fade" id="modalCatalogLead" tabindex="-1" aria-labelledby="modalCatalogLeadLabel" aria-hidden="true" style="z-index: 1080;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-pdf-fill fs-4 text-warning"></i>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalCatalogLeadLabel">Unduh E-Katalog &amp; Price List</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Silakan lengkapi data singkat di bawah ini. Berkas resmi PDF <strong>E-Katalog Kompilasi &amp; Rekap Price List</strong> akan langsung diunduh otomatis ke perangkat Anda.
                    </p>

                    <div id="catalogLeadAlert"></div>

                    <form id="formCatalogLead">
                        <?= csrf_field() ?>
                        <input type="hidden" name="property_id" value="">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nama Lengkap Anda <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="nama_prospek" id="catalogLeadName" class="form-control" placeholder="Contoh: Budi Santoso" required>
                            </div>
                            <div class="invalid-feedback" id="err_catalog_nama"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-muted"></i></span>
                                <input type="tel" name="no_wa" id="catalogLeadWa" class="form-control" placeholder="Contoh: 081234567890" required>
                            </div>
                            <div class="invalid-feedback" id="err_catalog_wa"></div>
                            <small class="text-muted" style="font-size: 0.72rem;">* Privasi aman, nomor Anda hanya digunakan untuk konfirmasi info unit &amp; e-brosur.</small>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2.5 rounded-pill fw-bold" id="btnSubmitCatalogLead">
                                <i class="bi bi-download me-1"></i> Verifikasi &amp; Unduh E-Katalog Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Auto-fill from localStorage if available
        try {
            var savedLead = JSON.parse(localStorage.getItem('va_user_lead') || '{}');
            if (savedLead.nama) {
                $('#catalogLeadName').val(savedLead.nama);
            }
            if (savedLead.phone) {
                $('#catalogLeadWa').val(savedLead.phone);
            }
        } catch(e) {}

        // Global AJAX Submission for E-Catalog Lead Magnet
        $(document).on('submit', '#formCatalogLead', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#btnSubmitCatalogLead');

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Memverifikasi &amp; mengunduh...');
            $('.invalid-feedback').text('').hide();
            $('.form-control').removeClass('is-invalid');
            $('#catalogLeadAlert').empty();

            $.ajax({
                url: "<?= site_url('leads/store') ?>",
                type: "POST",
                data: form.serialize(),
                dataType: "json",
                success: function(res) {
                    if (res.success) {
                        try {
                            var nameVal = $('#catalogLeadName').val().trim();
                            var waVal = $('#catalogLeadWa').val().trim();
                            localStorage.setItem('va_user_lead', JSON.stringify({
                                nama: nameVal,
                                phone: waVal,
                                submitted: true
                            }));
                        } catch(e) {}

                        $('#catalogLeadAlert').html(
                            '<div class="alert alert-success border-0 small"><i class="bi bi-check-circle-fill me-1"></i> ' + res.message + '</div>'
                        );

                        setTimeout(function() {
                            window.location.href = res.download_url;
                        }, 800);

                        setTimeout(function() {
                            var modalEl = document.getElementById('modalCatalogLead');
                            if (modalEl) {
                                var modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();
                            }
                            btn.prop('disabled', false).html('<i class="bi bi-download me-1"></i> Verifikasi &amp; Unduh E-Katalog Sekarang');
                            $('#catalogLeadAlert').empty();
                        }, 2400);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-download me-1"></i> Verifikasi &amp; Unduh E-Katalog Sekarang');
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        if (errors.nama_prospek) {
                            $('#catalogLeadName').addClass('is-invalid');
                            $('#err_catalog_nama').text(errors.nama_prospek).show();
                        }
                        if (errors.no_wa) {
                            $('#catalogLeadWa').addClass('is-invalid');
                            $('#err_catalog_wa').text(errors.no_wa).show();
                        }
                    } else {
                        $('#catalogLeadAlert').html(
                            '<div class="alert alert-danger border-0 small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kendala saat memproses data. Silakan coba kembali.</div>'
                        );
                    }
                }
            });
        });
    });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
