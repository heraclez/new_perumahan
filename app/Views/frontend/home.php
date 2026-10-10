<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>

<?php if (empty($sections)): ?>
    <!-- Fallback default rendering if sections table is empty -->
    <div class="alert alert-warning container my-4">
        Belum ada section aktif. Silakan buka Admin > Section Manager untuk mengatur section beranda.
    </div>
<?php else: ?>

    <?php foreach ($sections as $section): ?>
        <?php
        $secKey  = $section['section_key'];
        $content = $section['content'] ?? [];
        $variant = $section['layout_variant'] ?? 'default';
        $title   = $section['title'] ?? '';
        $sub     = $section['subtitle'] ?? '';
        $isAdminLoggedIn = session()->get('is_admin_logged_in') ?? false;

        $styling         = $content['styling'] ?? [];
        $secBgType       = $styling['bg_type'] ?? 'default';
        $secTextMode     = $styling['text_mode'] ?? 'auto';
        $secFontFamily   = $styling['font_family'] ?? 'default';
        $secFontWeight   = $styling['title_font_weight'] ?? 'default';
        $secTransform    = $styling['title_transform'] ?? 'default';
        $secShadow       = $styling['text_shadow'] ?? 'none';
        $secOverlay      = $styling['overlay_opacity'] ?? 'default';
        $secBorderRadius = $styling['border_radius'] ?? 'default';

        // Font family map
        $fontMap = [
            'plus_jakarta'      => "'Plus Jakarta Sans', sans-serif",
            'space_grotesk'     => "'Space Grotesk', sans-serif",
            'playfair'          => "'Playfair Display', Georgia, serif",
            'onest'             => "'Onest', sans-serif",
            'inter'             => "'Inter', sans-serif",
            'jetbrains'         => "'JetBrains Mono', monospace",
            'plus-jakarta-sans' => "'Plus Jakarta Sans', sans-serif",
            'space-grotesk'     => "'Space Grotesk', sans-serif",
            'playfair-display'  => "'Playfair Display', Georgia, serif",
            'jetbrains-mono'    => "'JetBrains Mono', monospace",
        ];
        $fontCss = (isset($fontMap[$secFontFamily]) && $secFontFamily !== 'default') ? "font-family: {$fontMap[$secFontFamily]} !important;" : "";

        // Font weight css
        $weightCss = '';
        if ($secFontWeight === 'bold') {
            $weightCss = 'font-weight: 700 !important;';
        } elseif ($secFontWeight === 'extrabold') {
            $weightCss = 'font-weight: 900 !important;';
        } elseif ($secFontWeight === 'normal') {
            $weightCss = 'font-weight: 500 !important;';
        }

        // Text transform css
        $transformCss = '';
        if ($secTransform === 'uppercase') {
            $transformCss = 'text-transform: uppercase !important; letter-spacing: 0.02em !important;';
        } elseif ($secTransform === 'capitalize') {
            $transformCss = 'text-transform: capitalize !important;';
        }

        // Text shadow css (crucial for contrast on top of photos)
        $shadowCss = '';
        if ($secShadow === 'subtle') {
            $shadowCss = 'text-shadow: 0 2px 8px rgba(0, 0, 0, 0.65) !important;';
        } elseif ($secShadow === 'strong') {
            $shadowCss = 'text-shadow: 0 3px 14px rgba(0, 0, 0, 0.95), 0 1px 3px rgba(0, 0, 0, 0.95) !important;';
        } elseif ($secShadow === 'glow') {
            $shadowCss = 'text-shadow: 0 0 16px rgba(255, 255, 255, 0.8) !important;';
        }

        // Calculate dynamic background style override
        $dynamicBgStyle = '';
        if ($secBgType === 'solid' && !empty($styling['bg_color'])) {
            $dynamicBgStyle = 'background: ' . esc($styling['bg_color']) . ' !important;';
        } elseif ($secBgType === 'gradient') {
            $from = !empty($styling['gradient_from']) ? $styling['gradient_from'] : 'var(--bs-primary)';
            $to = !empty($styling['gradient_to']) ? $styling['gradient_to'] : 'var(--bs-secondary)';
            $angle = !empty($styling['gradient_angle']) ? $styling['gradient_angle'] : '135deg';
            $dynamicBgStyle = "background: linear-gradient({$angle}, {$from}, {$to}) !important;";
        }

        // Title and Description Color
        $isPhotoBgHero = ($secKey === 'hero' && ($variant === 'full_image' || $variant === 'video_hero'));
        $isLightText = ($secTextMode === 'light') 
                    || ($secBgType === 'gradient' && $secTextMode === 'auto') 
                    || ($isPhotoBgHero && $secTextMode !== 'dark')
                    || ($secKey === 'cta' && $secTextMode !== 'dark');

        $titleColor = '';
        $descColor = '';
        if ($secTextMode === 'custom') {
            if (!empty($styling['custom_title_color'])) {
                $titleColor = 'color: ' . esc($styling['custom_title_color']) . ' !important;';
            }
            if (!empty($styling['custom_text_color'])) {
                $descColor = 'color: ' . esc($styling['custom_text_color']) . ' !important;';
            }
        } elseif ($secTextMode === 'dark') {
            $titleColor = 'color: #0f172a !important;';
            $descColor  = 'color: #334155 !important;';
        } elseif ($isLightText) {
            $titleColor = 'color: #ffffff !important;';
            $descColor  = 'color: rgba(255, 255, 255, 0.90) !important;';
        }

        // Auto defaults for photo background hero if not specified
        if ($isPhotoBgHero) {
            if (empty($titleColor)) {
                $titleColor = 'color: #ffffff !important;';
            }
            if (empty($descColor)) {
                $descColor = 'color: rgba(255, 255, 255, 0.90) !important;';
            }
            if (empty($shadowCss)) {
                $shadowCss = 'text-shadow: 0 2px 10px rgba(0, 0, 0, 0.75) !important;';
            }
        }

        $customTitleStyle = trim("{$titleColor} {$fontCss} {$weightCss} {$transformCss} {$shadowCss}");
        $customDescStyle  = trim("{$descColor} {$fontCss} {$shadowCss}");

        // Overlay darkness for photo backgrounds
        $heroOverlayBg = 'linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.84))';
        if ($secOverlay === 'light') {
            $heroOverlayBg = 'linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.45))';
        } elseif ($secOverlay === 'medium') {
            $heroOverlayBg = 'linear-gradient(rgba(15, 23, 42, 0.60), rgba(15, 23, 42, 0.70))';
        } elseif ($secOverlay === 'heavy') {
            $heroOverlayBg = 'linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.92))';
        } elseif ($secOverlay === 'none') {
            $heroOverlayBg = 'linear-gradient(rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.05))';
        }
        ?>

        <div class="position-relative cms-section-wrapper" data-section="<?= esc($secKey) ?>">
            <?php if ($secBorderRadius !== 'default'): ?>
            <style>
                <?php if ($secBorderRadius === 'pill'): ?>
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .card,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .card-custom {
                    border-radius: 20px !important;
                }
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .btn {
                    border-radius: 9999px !important;
                }
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .badge {
                    border-radius: 9999px !important;
                }
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] img.rounded-4,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] img.rounded-3 {
                    border-radius: 20px !important;
                }
                <?php elseif ($secBorderRadius === 'subtle'): ?>
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .card,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .card-custom {
                    border-radius: 10px !important;
                }
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .btn {
                    border-radius: 8px !important;
                }
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .badge {
                    border-radius: 6px !important;
                }
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] img.rounded-4,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] img.rounded-3 {
                    border-radius: 10px !important;
                }
                <?php elseif ($secBorderRadius === 'sharp'): ?>
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .card,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .card-custom,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .btn,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .badge,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .form-control,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] .form-select,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] img.rounded-4,
                .cms-section-wrapper[data-section="<?= esc($secKey) ?>"] img.rounded-3 {
                    border-radius: 0px !important;
                }
                <?php endif; ?>
            </style>
            <?php endif; ?>

            <?php if ($isAdminLoggedIn): ?>
            <div class="position-absolute top-0 end-0 m-2 m-md-3 z-3 d-print-none">
                <a href="<?= site_url('admin/sections/edit/' . $secKey) ?>" target="_blank" class="btn btn-sm btn-dark text-warning border border-warning shadow rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: rgba(15,23,42,0.92); backdrop-filter: blur(8px); font-size: 0.78rem; text-decoration: none;" title="Edit konten bagian <?= esc($title) ?>">
                    <i class="bi bi-pencil-square text-warning"></i>
                    <span>Edit <?= esc($title ?: ucfirst($secKey)) ?></span>
                    <span class="badge bg-warning text-dark rounded-pill py-0 px-1.5 ms-1" style="font-size: 0.65rem;"><?= esc($variant) ?></span>
                </a>
            </div>
            <?php endif; ?>

        <?php if ($secKey === 'hero'): ?>
            <!-- ================================================================= -->
            <!-- 1. HERO SECTION (Layout Variants: split, full_image, centered, editorial, video_hero) -->
            <!-- ================================================================= -->
            <?php
            $badgeText    = $content['badge_text'] ?? 'Developer Properti Terpercaya & Legalitas Aman (SHM)';
            $btn1Text     = $content['btn1_text'] ?? 'Jelajahi Tipe Rumah';
            $btn1Link     = $content['btn1_link'] ?? 'properti';
            $btn2Text     = $content['btn2_text'] ?? 'Hitung Simulasi KPR';
            $btn2Link     = $content['btn2_link'] ?? 'kpr-calculator';
            $stat1Val     = $content['stat1_value'] ?? '100%';
            $stat1Lbl     = $content['stat1_label'] ?? 'Sertifikat SHM';
            $stat2Val     = $content['stat2_value'] ?? 'DP 0%';
            $stat2Lbl     = $content['stat2_label'] ?? 'Promo Cicilan KPR';
            $stat3Val     = $content['stat3_value'] ?? '24/7';
            $stat3Lbl     = $content['stat3_label'] ?? 'Keamanan & CCTV';
            $mediaType    = $content['media_type'] ?? 'image';
            $heroImg      = !empty($content['image_url']) ? $content['image_url'] : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=900&auto=format&fit=crop&q=80';
            $focalX       = $content['focal_x'] ?? 'center';
            $focalY       = $content['focal_y'] ?? 'center';
            $aspectRatio  = $content['aspect_ratio'] ?? '4/3';
            $pricePrefix  = $content['price_badge_prefix'] ?? 'Harga Mulai Dari';
            $priceText    = $content['price_badge_text'] ?? 'Rp 650 Juta-an';
            $videoUrl     = $content['video_url'] ?? '';
            $videoSource  = $content['video_source'] ?? 'youtube';
            $focalStyle   = "object-fit: cover; object-position: {$focalX} {$focalY};";
            ?>

            <?php if ($variant === 'full_image' || ($mediaType === 'image' && $variant === 'video_hero' && empty($videoUrl))): ?>
                <!-- Hero Variant: Full Image Background with Overlay -->
                <section class="position-relative py-5 py-lg-6 hero-section text-white d-flex align-items-center" style="min-height: 80vh; background: <?= $heroOverlayBg ?>, url('<?= esc($heroImg) ?>') center/cover no-repeat;">
                    <div class="container py-5 text-center">
                        <div class="max-w-3xl mx-auto">
                            <?php if (!empty($badgeText)): ?>
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill mb-3 shadow-sm">
                                    <i class="bi bi-patch-check-fill text-primary me-1"></i> <?= esc($badgeText) ?>
                                </span>
                            <?php endif; ?>
                            <h1 class="display-3 fw-extrabold tracking-tight mb-3 text-white" style="<?= $customTitleStyle ?>">
                                <?= esc($title) ?>
                            </h1>
                            <p class="lead text-white-50 mb-4 fs-5 mx-auto" style="max-width: 700px; <?= $customDescStyle ?>">
                                <?= esc($sub) ?>
                            </p>
                            <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                                <a href="<?= site_url($btn1Link) ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                                    <i class="bi bi-houses me-2"></i> <?= esc($btn1Text) ?>
                                </a>
                                <a href="<?= site_url($btn2Link) ?>" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                    <i class="bi bi-calculator me-2"></i> <?= esc($btn2Text) ?>
                                </a>
                            </div>

                            <!-- Highlights Stats Banner -->
                            <div class="row g-3 justify-content-center border-top border-white border-opacity-25 pt-4 text-center">
                                <div class="col-4 col-md-3">
                                    <h3 class="fw-bold text-white mb-0"><?= esc($stat1Val) ?></h3>
                                    <small class="text-white-50"><?= esc($stat1Lbl) ?></small>
                                </div>
                                <div class="col-4 col-md-3 border-start border-white border-opacity-25 ps-3">
                                    <h3 class="fw-bold text-white mb-0"><?= esc($stat2Val) ?></h3>
                                    <small class="text-white-50"><?= esc($stat2Lbl) ?></small>
                                </div>
                                <div class="col-4 col-md-3 border-start border-white border-opacity-25 ps-3">
                                    <h3 class="fw-bold text-white mb-0"><?= esc($stat3Val) ?></h3>
                                    <small class="text-white-50"><?= esc($stat3Lbl) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            <?php elseif ($variant === 'video_hero' && !empty($videoUrl)): ?>
                <!-- Hero Variant: Video Background (YouTube / Vimeo / MP4) -->
                <section class="position-relative py-5 py-lg-6 hero-section text-white overflow-hidden d-flex align-items-center" style="min-height: 80vh; background-color: #0f172a;">
                    <?php if ($videoSource === 'mp4'): ?>
                        <video autoplay muted loop playsinline poster="<?= esc($heroImg) ?>" class="position-absolute w-100 h-100 top-0 start-0" style="object-fit: cover; opacity: 0.45; z-index: 0;">
                            <source src="<?= esc($videoUrl) ?>" type="video/mp4">
                        </video>
                    <?php elseif (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')): ?>
                        <?php
                        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $videoUrl, $ytMatches);
                        $ytId = $ytMatches[1] ?? '';
                        ?>
                        <div class="position-absolute w-100 h-100 top-0 start-0 pointer-events-none" style="opacity: 0.45; z-index: 0;">
                            <iframe src="https://www.youtube-nocookie.com/embed/<?= esc($ytId) ?>?autoplay=1&mute=1&controls=0&loop=1&playlist=<?= esc($ytId) ?>&playsinline=1" class="w-100 h-100 border-0" allow="autoplay; encrypted-media"></iframe>
                        </div>
                    <?php endif; ?>
                    <div class="container py-5 text-center position-relative" style="z-index: 1;">
                        <div class="max-w-3xl mx-auto">
                            <?php if (!empty($badgeText)): ?>
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill mb-3 shadow-sm">
                                    <i class="bi bi-play-circle-fill text-danger me-1"></i> <?= esc($badgeText) ?>
                                </span>
                            <?php endif; ?>
                            <h1 class="display-3 fw-extrabold tracking-tight mb-3 text-white" style="<?= $customTitleStyle ?>">
                                <?= esc($title) ?>
                            </h1>
                            <p class="lead text-white-50 mb-4 fs-5 mx-auto" style="max-width: 700px; <?= $customDescStyle ?>">
                                <?= esc($sub) ?>
                            </p>
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <a href="<?= site_url($btn1Link) ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                                    <?= esc($btn1Text) ?>
                                </a>
                                <a href="<?= site_url($btn2Link) ?>" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                    <?= esc($btn2Text) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

            <?php elseif ($variant === 'centered'): ?>
                <!-- Hero Variant: Centered Minimalist -->
                <section class="position-relative py-5 py-lg-6 hero-section text-center" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                    <div class="container py-4">
                        <div class="max-w-3xl mx-auto mb-4">
                            <?php if (!empty($badgeText)): ?>
                                <span class="badge badge-primary-soft px-3 py-2 rounded-pill mb-3">
                                    <i class="bi bi-patch-check-fill text-primary me-1"></i> <?= esc($badgeText) ?>
                                </span>
                            <?php endif; ?>
                            <h1 class="display-4 fw-extrabold text-dark tracking-tight mb-3" <?= !empty($customTitleStyle) ? 'style="' . $customTitleStyle . '"' : '' ?>>
                                <?= esc($title) ?>
                            </h1>
                            <p class="lead <?= $isLightText ? 'text-white-50' : 'text-muted' ?> mb-4 fs-5" <?= !empty($customDescStyle) ? 'style="' . $customDescStyle . '"' : '' ?>>
                                <?= esc($sub) ?>
                            </p>
                            <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                                <a href="<?= site_url($btn1Link) ?>" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                                    <?= esc($btn1Text) ?>
                                </a>
                                <a href="<?= site_url($btn2Link) ?>" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                                    <?= esc($btn2Text) ?>
                                </a>
                            </div>
                        </div>

                        <div class="position-relative rounded-4 overflow-hidden shadow-lg mx-auto" style="max-width: 960px;">
                            <img src="<?= esc($heroImg) ?>" alt="<?= esc($title) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" fetchpriority="high" decoding="async" class="w-100 rounded-4" style="max-height: 480px; <?= $focalStyle ?>">
                            <div class="position-absolute bottom-0 start-0 m-4 p-3 bg-white rounded-3 shadow d-flex align-items-center gap-3 text-start">
                                <div>
                                    <small class="text-muted d-block"><?= esc($pricePrefix) ?></small>
                                    <span class="fs-5 fw-bold text-primary"><?= esc($priceText) ?></span>
                                </div>
                                <a href="<?= site_url('properti') ?>" class="btn btn-sm btn-primary rounded-pill px-3">Lihat Unit</a>
                            </div>
                        </div>
                    </div>
                </section>

            <?php elseif ($variant === 'editorial'): ?>
                <!-- Hero Variant: Editorial Architecture Style -->
                <section class="position-relative py-5 py-lg-6 hero-section" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                    <div class="container py-4">
                        <div class="row align-items-end mb-4 border-bottom pb-3">
                            <div class="col-lg-8">
                                <div class="text-uppercase small text-muted font-monospace mb-2 letter-spacing-1">
                                    01 // ARCHITECTURAL SHOWCASE
                                </div>
                                <h1 class="display-3 fw-bold mb-0 tracking-tight" style="font-family: 'Space Grotesk', sans-serif; <?= $customTitleStyle ?>">
                                    <?= esc($title) ?>
                                </h1>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                                <div class="d-flex gap-2 justify-content-lg-end">
                                    <a href="<?= site_url($btn1Link) ?>" class="btn btn-dark rounded-0 px-4 py-2 fw-bold">
                                        <?= esc($btn1Text) ?> &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="position-relative border border-dark p-2 bg-white">
                                    <img src="<?= esc($heroImg) ?>" alt="<?= esc($title) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" fetchpriority="high" decoding="async" class="img-fluid w-100" style="max-height: 480px; <?= $focalStyle ?>">
                                    <div class="position-absolute top-0 end-0 bg-dark text-white px-3 py-2 small font-monospace">
                                        <?= esc($priceText) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 d-flex flex-column justify-content-between">
                                <p class="lead <?= $isLightText ? 'text-white-50' : 'text-muted' ?> fs-6" style="line-height: 1.8; <?= $customDescStyle ?>">
                                    <?= esc($sub) ?>
                                </p>
                                <div class="border-top pt-4">
                                    <div class="row g-3">
                                        <div class="col-4">
                                            <h3 class="fw-bold font-monospace text-dark mb-0"><?= esc($stat1Val) ?></h3>
                                            <small class="text-muted"><?= esc($stat1Lbl) ?></small>
                                        </div>
                                        <div class="col-4 border-start ps-3">
                                            <h3 class="fw-bold font-monospace text-dark mb-0"><?= esc($stat2Val) ?></h3>
                                            <small class="text-muted"><?= esc($stat2Lbl) ?></small>
                                        </div>
                                        <div class="col-4 border-start ps-3">
                                            <h3 class="fw-bold font-monospace text-dark mb-0"><?= esc($stat3Val) ?></h3>
                                            <small class="text-muted"><?= esc($stat3Lbl) ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            <?php else: ?>
                <!-- Hero Variant: Split 2-Kolom (Default Existing) -->
                <section class="position-relative py-5 py-lg-6 hero-section" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                    <div class="container py-4">
                        <div class="row align-items-center g-5">
                            <div class="col-lg-6">
                                <?php if (!empty($badgeText)): ?>
                                    <span class="badge badge-primary-soft px-3 py-2 rounded-pill mb-3">
                                        <i class="bi bi-patch-check-fill text-primary me-1"></i> <?= esc($badgeText) ?>
                                    </span>
                                <?php endif; ?>
                                <h1 class="display-4 fw-extrabold text-dark tracking-tight mb-3" <?= !empty($customTitleStyle) ? 'style="' . $customTitleStyle . '"' : '' ?>>
                                    <?= esc($title) ?>
                                </h1>
                                <p class="lead <?= $isLightText ? 'text-white-50' : 'text-muted' ?> mb-4" <?= !empty($customDescStyle) ? 'style="' . $customDescStyle . '"' : '' ?>>
                                    <?= esc($sub) ?>
                                </p>
                                <div class="d-flex flex-wrap gap-3 mb-5">
                                    <a href="<?= site_url($btn1Link) ?>" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                                        <i class="bi bi-houses me-2"></i> <?= esc($btn1Text) ?>
                                    </a>
                                    <a href="<?= site_url($btn2Link) ?>" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                                        <i class="bi bi-calculator me-2"></i> <?= esc($btn2Text) ?>
                                    </a>
                                </div>

                                <!-- Highlight Stats -->
                                <div class="row g-3 border-top pt-4">
                                    <div class="col-4">
                                        <h4 class="fw-bold text-primary mb-0"><?= esc($stat1Val) ?></h4>
                                        <small class="text-muted"><?= esc($stat1Lbl) ?></small>
                                    </div>
                                    <div class="col-4 border-start ps-3">
                                        <h4 class="fw-bold text-primary mb-0"><?= esc($stat2Val) ?></h4>
                                        <small class="text-muted"><?= esc($stat2Lbl) ?></small>
                                    </div>
                                    <div class="col-4 border-start ps-3">
                                        <h4 class="fw-bold text-primary mb-0"><?= esc($stat3Val) ?></h4>
                                        <small class="text-muted"><?= esc($stat3Lbl) ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="position-relative">
                                    <div class="card card-custom border-0 overflow-hidden shadow-lg p-2 bg-white">
                                        <img src="<?= esc($heroImg) ?>" alt="<?= esc($title) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" fetchpriority="high" decoding="async" class="rounded-4 img-fluid" style="max-height: 440px; <?= $focalStyle ?>">
                                        <div class="p-3 bg-white d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted d-block"><?= esc($pricePrefix) ?></small>
                                                <span class="fs-4 fw-bold text-primary"><?= esc($priceText) ?></span>
                                            </div>
                                            <a href="<?= site_url('properti') ?>" class="btn btn-sm btn-primary rounded-pill px-3">Lihat Detail <i class="bi bi-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

        <?php elseif ($secKey === 'properties'): ?>
            <!-- ================================================================= -->
            <!-- 2. PROPERTIES SECTION (Tipe Rumah & Klaster) -->
            <!-- ================================================================= -->
            <?php
            $propBtnText = $content['btn_text'] ?? 'Lihat Semua Tipe Rumah';
            $propBtnLink = $content['btn_link'] ?? 'properti';
            $limit       = (int) ($content['limit'] ?? 6);

            $colClass = 'col-md-6 col-lg-4';
            if ($variant === '2_col') {
                $colClass = 'col-md-6';
            } elseif ($variant === '4_col') {
                $colClass = 'col-md-6 col-lg-3';
            }
            ?>
            <section class="py-5 bg-light-subtle properties-section" id="properti" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                <div class="container py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
                        <div>
                            <?php if (!empty($sub)): ?>
                                <span class="fw-bold text-uppercase small letter-spacing-1" style="<?= $customDescStyle ?: 'color: var(--bs-primary);' ?>"><?= esc($sub) ?></span>
                            <?php endif; ?>
                            <h2 class="fw-bold text-dark mb-0" <?= !empty($customTitleStyle) ? 'style="' . $customTitleStyle . '"' : '' ?>><?= esc($title) ?></h2>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <a href="<?= site_url($propBtnLink) ?>" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
                                <?= esc($propBtnText) ?> <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="row g-4">
                        <?php if (empty($featuredProperties)): ?>
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">Belum ada unit properti yang dipublikasikan.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($featuredProperties, 0, $limit) as $idx => $prop): ?>
                                <?php
                                $imgSrc = 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&auto=format&fit=crop&q=80';
                                if (!empty($prop['images']) && is_array($prop['images'])) {
                                    $imgSrc = base_url('uploads/properties/' . $prop['images'][0]['image_name']);
                                } elseif (!empty($prop['main_image'])) {
                                    $imgSrc = base_url('uploads/properties/' . $prop['main_image']);
                                }

                                $cardCol = $colClass;
                                if ($variant === 'featured_grid' && $idx === 0) {
                                    $cardCol = 'col-lg-6';
                                } elseif ($variant === 'featured_grid') {
                                    $cardCol = 'col-md-6 col-lg-3';
                                }
                                ?>
                                <div class="<?= $cardCol ?>">
                                    <div class="card card-custom h-100 overflow-hidden bg-white">
                                        <div class="position-relative">
                                            <img src="<?= esc($imgSrc) ?>" class="card-img-top" alt="Rumah <?= esc($prop['title']) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" style="height: <?= ($variant === 'featured_grid' && $idx === 0) ? '320px' : '240px' ?>; object-fit: cover;">
                                            
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
                                                <?= character_limiter(strip_tags($prop['deskripsi'] ?? ''), 95) ?>
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

                                            <div class="d-flex gap-2">
                                                <a href="<?= site_url('properti/' . $prop['slug']) ?>" class="btn btn-primary w-100 rounded-pill py-2">
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($secKey === 'about'): ?>
            <!-- ================================================================= -->
            <!-- 3. ABOUT SECTION (Layout Variants: split, centered, editorial) -->
            <!-- ================================================================= -->
            <?php
            $aboutDesc = $content['description'] ?? 'Grand Harmoni Residence menghadirkan kawasan hunian berstandar tinggi yang menggabungkan keselarasan alam dengan infrastruktur modern.';
            $h1 = $content['highlight_1'] ?? 'Konstruksi bangunan terstandarisasi mutu SNI dengan pondasi kokoh.';
            $h2 = $content['highlight_2'] ?? 'Sistem utilitas bawah tanah (underground utilities) rapi dan terawat.';
            $h3 = $content['highlight_3'] ?? 'Aksesibilitas prima hanya beberapa menit ke fasilitas publik utama.';
            $aboutImg = !empty($content['image_url']) ? $content['image_url'] : 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=900&auto=format&fit=crop&q=80';
            $expNum = $content['experience'] ?? '15+ Tahun';
            $expLbl = $content['exp_label'] ?? 'Pengalaman Membangun Hunian Berkualitas';
            ?>

            <section class="py-5 about-section" id="tentang" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                <div class="container py-4">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-6">
                            <div class="position-relative">
                                <img src="<?= esc($aboutImg) ?>" alt="<?= esc($title) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 460px; object-fit: cover;">
                                <?php if (!empty($expNum)): ?>
                                    <div class="position-absolute bottom-0 start-0 m-4 p-3 bg-primary text-white rounded-3 shadow-lg" style="max-width: 240px;">
                                        <h3 class="fw-extrabold mb-0 text-white"><?= esc($expNum) ?></h3>
                                        <small class="text-white-50" style="font-size: 0.78rem;"><?= esc($expLbl) ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <?php if (!empty($sub)): ?>
                                <span class="fw-bold text-uppercase small letter-spacing-1" style="<?= $customDescStyle ?: 'color: var(--bs-primary);' ?>"><?= esc($sub) ?></span>
                            <?php endif; ?>
                            <h2 class="fw-bold text-dark mb-4" <?= !empty($customTitleStyle) ? 'style="' . $customTitleStyle . '"' : '' ?>><?= esc($title) ?></h2>
                            <p class="lead <?= $isLightText ? 'text-white-50' : 'text-muted' ?> mb-4 fs-6" <?= !empty($customDescStyle) ? 'style="' . $customDescStyle . '"' : '' ?>>
                                <?= nl2br(esc($aboutDesc)) ?>
                            </p>

                            <div class="d-flex flex-column gap-3 mb-4">
                                <?php if (!empty($h1)): ?>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-primary-soft text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="bi bi-check-lg fw-bold"></i>
                                        </div>
                                        <div>
                                            <span class="text-dark fw-medium"><?= esc($h1) ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($h2)): ?>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-primary-soft text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="bi bi-check-lg fw-bold"></i>
                                        </div>
                                        <div>
                                            <span class="text-dark fw-medium"><?= esc($h2) ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($h3)): ?>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-primary-soft text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="bi bi-check-lg fw-bold"></i>
                                        </div>
                                        <div>
                                            <span class="text-dark fw-medium"><?= esc($h3) ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a href="<?= site_url('properti') ?>" class="btn btn-outline-primary rounded-pill px-4">
                                Pelajari Konsep Kawasan <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ($secKey === 'features'): ?>
            <!-- ================================================================= -->
            <!-- 4. FEATURES / USP SECTION (Layout Variants: grid_4, grid_3, card_horizontal) -->
            <!-- ================================================================= -->
            <?php
            $items = $content['items'] ?? [];
            $intro = $content['intro'] ?? 'Dirancang dengan standar konstruksi tinggi dan tata ruang lingkungan terpadu.';
            $fCol = $variant === 'grid_3' ? 'col-md-4' : 'col-md-6 col-lg-3';
            ?>

            <section class="py-5 usp-section" id="keunggulan" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                <div class="container py-4">
                    <div class="text-center max-w-2xl mx-auto mb-5">
                        <?php if (!empty($sub)): ?>
                            <span class="fw-bold text-uppercase small" style="<?= $customDescStyle ?: 'color: var(--bs-primary);' ?>"><?= esc($sub) ?></span>
                        <?php endif; ?>
                        <h2 class="fw-bold text-dark" <?= !empty($customTitleStyle) ? 'style="' . $customTitleStyle . '"' : '' ?>><?= esc($title) ?></h2>
                        <p class="<?= $isLightText ? 'text-white-50' : 'text-muted' ?>" <?= !empty($customDescStyle) ? 'style="' . $customDescStyle . '"' : '' ?>><?= esc($intro) ?></p>
                    </div>

                    <div class="row g-4">
                        <?php foreach ($items as $item): ?>
                            <div class="<?= $fCol ?>">
                                <div class="card card-custom border-0 p-4 h-100 text-center bg-white">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi <?= esc($item['icon'] ?: 'bi-check-circle') ?> fs-3"></i>
                                    </div>
                                    <h5 class="fw-bold"><?= esc($item['title']) ?></h5>
                                    <p class="text-muted small mb-0"><?= esc($item['desc']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($secKey === 'gallery'): ?>
            <!-- ================================================================= -->
            <!-- 5. GALLERY SECTION (Layout Variants: masonry, grid, large_small) -->
            <!-- ================================================================= -->
            <?php
            $galleryItems = $content['items'] ?? [];
            ?>
            <section class="py-5 gallery-section <?= $secBgType === 'default' ? 'bg-light' : '' ?>" id="galeri" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                <div class="container py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
                        <div>
                            <?php if (!empty($sub)): ?>
                                <span class="fw-bold text-uppercase small letter-spacing-1" style="<?= $customDescStyle ?: 'color: var(--bs-primary);' ?>"><?= esc($sub) ?></span>
                            <?php endif; ?>
                            <h2 class="fw-bold text-dark mb-0" <?= !empty($customTitleStyle) ? 'style="' . $customTitleStyle . '"' : '' ?>><?= esc($title) ?></h2>
                        </div>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($galleryItems as $gIdx => $g): ?>
                            <?php
                            $gCol = 'col-6 col-md-4';
                            if ($variant === 'large_small' && $gIdx === 0) {
                                $gCol = 'col-12 col-md-8';
                            }
                            ?>
                            <div class="<?= $gCol ?>">
                                <div class="card border-0 rounded-4 overflow-hidden shadow-xs h-100 position-relative group-hover">
                                    <img src="<?= esc($g['image']) ?>" alt="<?= esc($g['caption']) ?: 'Fasilitas Kawasan' ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" class="w-100 h-100 object-fit-cover" style="min-height: 220px; max-height: 300px; transition: transform 0.3s ease;">
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-dark text-white d-flex justify-content-between align-items-end" style="background: linear-gradient(transparent, rgba(0,0,0,0.75));">
                                        <div>
                                            <span class="badge bg-primary rounded-pill mb-1" style="font-size: 0.7rem;"><?= esc($g['tag'] ?? 'Fasilitas') ?></span>
                                            <div class="fw-bold text-white small"><?= esc($g['caption']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($secKey === 'siteplan'): ?>
            <!-- ================================================================= -->
            <!-- 6. SITEPLAN SECTION (Interactive Masterplan Showcase) -->
            <!-- ================================================================= -->
            <?php
            $spDesc = $content['description'] ?? 'Klik kavling untuk melihat ketersediaan unit.';
            $spBtn  = $content['btn_text'] ?? 'Buka Denah Masterplan Lengkap';
            $spLink = $content['btn_link'] ?? 'siteplan';
            $firstSp = $activeSiteplans[0] ?? null;
            $spImg = $firstSp ? base_url('uploads/siteplan/' . $firstSp['image']) : 'https://images.unsplash.com/photo-1524813686514-a57563d77d66?w=1200&auto=format&fit=crop&q=80';
            ?>
            <section class="py-5 siteplan-section" id="siteplan" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                <div class="container py-4">
                    <div class="card card-custom border-0 overflow-hidden shadow-lg p-4 p-lg-5 bg-white">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-5">
                                <?php if (!empty($sub)): ?>
                                    <span class="fw-bold text-uppercase small letter-spacing-1" style="<?= $customDescStyle ?: 'color: var(--bs-primary);' ?>"><?= esc($sub) ?></span>
                                <?php endif; ?>
                                <h2 class="fw-bold text-dark mb-3" <?= !empty($customTitleStyle) ? 'style="' . $customTitleStyle . '"' : '' ?>><?= esc($title) ?></h2>
                                <p class="<?= $isLightText ? 'text-white-50' : 'text-muted' ?> mb-4" <?= !empty($customDescStyle) ? 'style="' . $customDescStyle . '"' : '' ?>><?= esc($spDesc) ?></p>

                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Hijau: Tersedia</span>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Kuning: Booking</span>
                                    <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Merah: Terjual</span>
                                </div>

                                <a href="<?= site_url($spLink) ?>" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-map-fill me-2"></i> <?= esc($spBtn) ?>
                                </a>
                            </div>
                            <div class="col-lg-7">
                                <div class="position-relative rounded-4 overflow-hidden border shadow-sm bg-light text-center">
                                    <img src="<?= esc($spImg) ?>" alt="Denah Masterplan Siteplan - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" class="img-fluid w-100" style="max-height: 380px; object-fit: cover;">
                                    <div class="position-absolute bottom-0 end-0 m-3">
                                        <a href="<?= site_url($spLink) ?>" class="btn btn-sm btn-light text-primary fw-bold rounded-pill shadow">
                                            <i class="bi bi-zoom-in me-1"></i> Mode Interaktif
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ($secKey === 'promo'): ?>
            <!-- ================================================================= -->
            <!-- 7. PROMO SECTION (In-stream Promo Banner) -->
            <!-- ================================================================= -->
            <?php
            $pBadge = $content['badge'] ?? 'KUOTA TERBATAS';
            $pDesc  = $content['desc'] ?? 'Dapatkan diskon DP dan subsidi bunga KPR.';
            $pBtn   = $content['btn_text'] ?? 'Klaim Promo via WhatsApp';
            $pImg   = !empty($content['image_url']) ? $content['image_url'] : 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&auto=format&fit=crop&q=60';
            ?>
            <section class="py-5 promo-banner-section" <?= !empty($dynamicBgStyle) ? 'style="' . $dynamicBgStyle . '"' : '' ?>>
                <div class="container py-2">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-lg" style="background: linear-gradient(135deg, var(--bs-primary), #0f172a);">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-5">
                                <img src="<?= esc($pImg) ?>" alt="<?= esc($title) ?> - <?= esc($settings['company_name'] ?? 'Grand Harmoni') ?>" loading="lazy" decoding="async" class="w-100 h-100 object-fit-cover" style="min-height: 280px; max-height: 360px;">
                            </div>
                            <div class="col-md-7 p-4 p-lg-5 text-white">
                                <span class="badge bg-danger text-white rounded-pill px-3 py-1.5 mb-3 fw-bold">
                                    <i class="bi bi-fire text-warning me-1"></i> <?= esc($pBadge) ?>
                                </span>
                                <h3 class="fw-bold text-white mb-2"><?= esc($title) ?></h3>
                                <p class="text-white-50 mb-4"><?= nl2br(esc($pDesc)) ?></p>
                                <a href="https://wa.me/<?= esc($settings['company_whatsapp'] ?? '') ?>?text=Halo%20<?= urlencode($settings['company_name'] ?? '') ?>,%20saya%20ingin%20mengklaim%20promo%20<?= urlencode($title) ?>" target="_blank" class="btn btn-warning text-dark btn-lg rounded-pill px-4 fw-bold shadow">
                                    <i class="bi bi-whatsapp me-2"></i> <?= esc($pBtn) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ($secKey === 'cta'): ?>
            <!-- ================================================================= -->
            <!-- 8. CTA SECTION (Layout Variants: center, split_image, bg_image) -->
            <!-- ================================================================= -->
            <?php
            $ctaDesc = $content['desc'] ?? 'Konsultasikan kebutuhan hunian, simulasi KPR gratis, dan jadwalkan survey show unit.';
            $ctaBtn1 = $content['btn1_text'] ?? 'Jadwalkan Survey Lokasi';
            $ctaBtn2 = $content['btn2_text'] ?? 'Unduh E-Katalog PDF';
            $ctaLink2 = $content['btn2_link'] ?? 'brochure/download-global';
            $bgType  = $content['bg_type'] ?? 'gradient';
            $bgImg   = !empty($content['bg_image']) ? $content['bg_image'] : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1600&auto=format&fit=crop&q=80';

            $ctaBgStyle = "background: linear-gradient(135deg, var(--bs-primary), var(--bs-secondary));";
            if (!empty($dynamicBgStyle)) {
                $ctaBgStyle = $dynamicBgStyle;
            } elseif ($bgType === 'image') {
                $ctaBgStyle = "background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.88)), url('{$bgImg}') center/cover no-repeat;";
            }
            ?>

            <section class="py-5 text-white position-relative cta-section" style="<?= $ctaBgStyle ?>">
                <div class="container py-4 text-center">
                    <h2 class="display-6 fw-bold mb-3 text-white" style="<?= $customTitleStyle ?: 'color: #ffffff !important;' ?>"><?= esc($title) ?></h2>
                    <p class="lead mb-4 mx-auto" style="max-width: 650px; <?= $customDescStyle ?: 'color: rgba(255, 255, 255, 0.88) !important;' ?>">
                        <?= esc($ctaDesc) ?>
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="https://wa.me/<?= esc($settings['company_whatsapp'] ?? '') ?>?text=Halo%20<?= urlencode($settings['company_name'] ?? 'Grand Harmoni') ?>,%20saya%20ingin%20jadwal%20survey%20lokasi." target="_blank" class="btn btn-light text-primary btn-lg rounded-pill px-4 fw-bold">
                            <i class="bi bi-whatsapp me-2"></i> <?= esc($ctaBtn1) ?>
                        </a>
                        <button type="button" class="btn btn-outline-light btn-lg rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCatalogLead">
                            <i class="bi bi-file-earmark-pdf me-2"></i> <?= esc($ctaBtn2) ?>
                        </button>
                    </div>
                </div>
            </section>

        <?php endif; ?>

        </div> <!-- .cms-section-wrapper -->

    <?php endforeach; ?>

<?php endif; ?>

<?= $this->endSection() ?>
