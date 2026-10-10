<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>



<form action="<?= site_url('admin/settings/update') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Left: Identity & Contact -->
        <div class="col-lg-6">
            <!-- Identitas PT / Perusahaan -->
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-building text-primary me-2"></i>Identitas Developer & Perumahan</h5>
                
                <div class="mb-3">
                    <label class="form-label">Nama Perusahaan / Proyek <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="<?= old('company_name', $settings['company_name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tagline Utama (Slogan)</label>
                    <input type="text" name="company_tagline" class="form-control" value="<?= old('company_tagline', $settings['company_tagline'] ?? '') ?>">
                </div>

                <!-- Logo Perusahaan -->
                <div class="mb-3 p-3 bg-light rounded-3 border">
                    <label class="form-label text-dark mb-1 d-flex align-items-center gap-1 fw-bold">
                        <i class="bi bi-image text-primary"></i> Logo Resmi Perusahaan / Perumahan
                    </label>
                    <p class="small mb-3" style="color: #64748b; font-size: 0.78rem;">
                        Ditampilkan pada Navbar Header, Footer, dan Sidebar Panel Admin. Format PNG transparan, WebP, atau SVG.
                    </p>

                    <?php 
                    $currLogo = $settings['company_logo'] ?? '';
                    $logoSrc = '';
                    if (!empty($currLogo)) {
                        if (str_starts_with($currLogo, 'http://') || str_starts_with($currLogo, 'https://')) {
                            $logoSrc = $currLogo;
                        } elseif (file_exists(FCPATH . 'uploads/settings/' . $currLogo)) {
                            $logoSrc = base_url('uploads/settings/' . $currLogo);
                        } elseif (file_exists(FCPATH . ltrim($currLogo, '/'))) {
                            $logoSrc = base_url(ltrim($currLogo, '/'));
                        }
                    }
                    ?>

                    <div class="d-flex flex-wrap align-items-center gap-3 p-3 bg-white rounded-3 border mb-2">
                        <div class="bg-light p-2 rounded border d-flex align-items-center justify-content-center" style="width: 120px; height: 70px;">
                            <img id="companyLogoPreviewImg" src="<?= esc($logoSrc) ?>" alt="Logo Perusahaan" class="img-fluid <?= empty($logoSrc) ? 'd-none' : '' ?>" style="max-height: 56px; max-width: 100%; object-fit: contain;">
                            <span id="logoPlaceholderBox" class="text-muted small text-center <?= !empty($logoSrc) ? 'd-none' : '' ?>">
                                <i class="bi bi-image fs-4 d-block text-secondary opacity-50"></i>
                                Belum ada logo
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-dark text-truncate mb-2" id="logoFilenameDisplay">
                                <?= !empty($currLogo) ? esc(basename($currLogo)) : 'Default (Teks Nama Perusahaan)' ?>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-primary" onclick="openMediaPicker('company_logo_url', 'companyLogoPreviewImg')">
                                    <i class="bi bi-folder2-open me-1"></i> Pilih dari File Manager
                                </button>
                                <label class="btn btn-sm btn-outline-secondary mb-0" style="cursor: pointer;">
                                    <i class="bi bi-upload me-1"></i> Unggah Berkas
                                    <input type="file" name="company_logo" id="companyLogoInput" class="d-none" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-danger <?= empty($logoSrc) ? 'd-none' : '' ?>" id="btnClearLogo" onclick="clearLogoSelection()">
                                    <i class="bi bi-trash me-1"></i> Hapus Logo
                                </button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="company_logo_url" id="company_logo_url" value="">
                    <input type="checkbox" name="delete_company_logo" id="deleteLogoCheck" value="1" class="d-none">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tentang Singkat Developer (Footer & SEO)</label>
                    <textarea name="company_about" class="form-control" rows="3"><?= old('company_about', $settings['company_about'] ?? '') ?></textarea>
                </div>

                <div class="mb-0">
                    <label class="form-label">Alamat Lengkap / Kantor Pemasaran</label>
                    <textarea name="company_address" class="form-control" rows="2"><?= old('company_address', $settings['company_address'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Kontak Pemasaran -->
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-telephone text-primary me-2"></i>Kontak Resmi Perusahaan</h5>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp Utama (Format: 628...) <span class="text-danger">*</span></label>
                        <input type="text" name="company_whatsapp" class="form-control" value="<?= old('company_whatsapp', $settings['company_whatsapp'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon Kantor</label>
                        <input type="text" name="company_phone" class="form-control" value="<?= old('company_phone', $settings['company_phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label">Email Pemasaran</label>
                    <input type="email" name="company_email" class="form-control" value="<?= old('company_email', $settings['company_email'] ?? '') ?>">
                </div>
            </div>

            <!-- Media Sosial Resmi -->
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-share text-primary me-2"></i>Media Sosial Resmi</h5>
                <p class="small text-muted mb-3">Tautan media sosial yang akan ditampilkan pada footer website publik. Kosongkan jika belum tersedia.</p>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-instagram text-danger me-1"></i> Instagram URL</label>
                        <input type="url" name="social_instagram" class="form-control" placeholder="https://instagram.com/username" value="<?= old('social_instagram', $settings['social_instagram'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-facebook text-primary me-1"></i> Facebook URL</label>
                        <input type="url" name="social_facebook" class="form-control" placeholder="https://facebook.com/username" value="<?= old('social_facebook', $settings['social_facebook'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-0">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-tiktok text-dark me-1"></i> TikTok URL</label>
                        <input type="url" name="social_tiktok" class="form-control" placeholder="https://tiktok.com/@username" value="<?= old('social_tiktok', $settings['social_tiktok'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-youtube text-danger me-1"></i> YouTube URL</label>
                        <input type="url" name="social_youtube" class="form-control" placeholder="https://youtube.com/@channel" value="<?= old('social_youtube', $settings['social_youtube'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Preset Gaya Desain (6 Pilihan Tema Eksklusif) -->
            <div class="card p-4 shadow-sm mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-brush text-primary me-2"></i>Pilihan Tema Desain Website</h5>
                    <span class="badge bg-primary px-3 py-1.5 rounded-pill">6 Pilihan Tema</span>
                </div>
                <p class="small mb-3" style="color: #64748b;">
                    Pilih arah estetika dan identitas visual website perumahan Anda. Semua tema menggunakan data katalog, galeri, dan CMS yang sama.
                </p>
                
                <?php 
                $currentTheme = $settings['site_theme_preset'] ?? 'modern-residential';
                if ($currentTheme === 'industrialist') $currentTheme = 'modern-residential';
                if ($currentTheme === 'brutalist') $currentTheme = 'soft-brutalist';
                ?>

                <div class="row g-3">
                    <!-- Theme 1: Modern Residential -->
                    <div class="col-md-6">
                        <label class="d-block h-100 p-3 rounded-3 border <?= $currentTheme === 'modern-residential' ? 'border-primary bg-primary-subtle' : 'bg-white' ?>" style="cursor: pointer; transition: all 0.2s ease; border-width: 2px !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-house-heart-fill text-primary fs-5"></i> Modern Residential
                                </span>
                                <input type="radio" name="site_theme_preset" value="modern-residential" class="form-check-input mt-0" <?= $currentTheme === 'modern-residential' ? 'checked' : '' ?>>
                            </div>
                            <p class="small mb-3" style="color: #475569; font-size: 0.82rem; line-height: 1.45;">
                                Clean, modern, ramah keluarga, warm, whitespace lapang, foto besar, rounded cards &amp; pill buttons.
                            </p>
                            <div class="d-flex gap-1.5 align-items-center flex-wrap">
                                <span class="badge" style="background: #1e3a8a; color: #fff;">#1e3a8a Navy</span>
                                <span class="badge" style="background: #0d9488; color: #fff;">#0d9488 Teal</span>
                                <span class="badge" style="background: #e2e8f0; color: #0f172a;">18px Pill</span>
                            </div>
                        </label>
                    </div>

                    <!-- Theme 2: Soft Brutalist -->
                    <div class="col-md-6">
                        <label class="d-block h-100 p-3 border <?= $currentTheme === 'soft-brutalist' ? 'border-dark bg-secondary-subtle' : 'bg-white' ?>" style="cursor: pointer; transition: all 0.2s ease; border: 2.5px solid #000000 !important; border-radius: 4px !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-square-fill text-dark fs-5"></i> Soft Brutalist
                                </span>
                                <input type="radio" name="site_theme_preset" value="soft-brutalist" class="form-check-input mt-0" <?= $currentTheme === 'soft-brutalist' ? 'checked' : '' ?>>
                            </div>
                            <p class="small mb-3" style="color: #000000; font-size: 0.82rem; line-height: 1.45; font-weight: 600;">
                                Typography besar (Space Grotesk), grid tegas, border 2.5px solid hitam, hard shadow, architectural bold.
                            </p>
                            <div class="d-flex gap-1.5 align-items-center flex-wrap">
                                <span class="badge" style="background: #000000; color: #fff;">#000000 Black</span>
                                <span class="badge" style="background: #ff3d00; color: #fff;">#ff3d00 Hazard</span>
                                <span class="badge" style="background: #fff; color: #000; border: 1.5px solid #000;">Hard Shadow</span>
                            </div>
                        </label>
                    </div>

                    <!-- Theme 3: Modern Luxury -->
                    <div class="col-md-6">
                        <label class="d-block h-100 p-3 rounded-3 border <?= $currentTheme === 'modern-luxury' ? 'border-warning bg-warning-subtle' : 'bg-white' ?>" style="cursor: pointer; transition: all 0.2s ease; border-width: 2px !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-gem text-warning fs-5"></i> Modern Luxury
                                </span>
                                <input type="radio" name="site_theme_preset" value="modern-luxury" class="form-check-input mt-0" <?= $currentTheme === 'modern-luxury' ? 'checked' : '' ?>>
                            </div>
                            <p class="small mb-3" style="color: #475569; font-size: 0.82rem; line-height: 1.45;">
                                Premium, minimal, elegan, whitespace luas, aksen champagne gold, tipografi halus, image dominant.
                            </p>
                            <div class="d-flex gap-1.5 align-items-center flex-wrap">
                                <span class="badge" style="background: #0f172a; color: #fff;">#0f172a Obsidian</span>
                                <span class="badge" style="background: #c59b27; color: #fff;">#c59b27 Gold</span>
                                <span class="badge" style="background: #fdfcf9; color: #0f172a; border: 1px solid #c59b27;">Refined</span>
                            </div>
                        </label>
                    </div>

                    <!-- Theme 4: Tropical Modern -->
                    <div class="col-md-6">
                        <label class="d-block h-100 p-3 rounded-3 border <?= $currentTheme === 'tropical-modern' ? 'border-success bg-success-subtle' : 'bg-white' ?>" style="cursor: pointer; transition: all 0.2s ease; border-width: 2px !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-tree-fill text-success fs-5"></i> Tropical Modern
                                </span>
                                <input type="radio" name="site_theme_preset" value="tropical-modern" class="form-check-input mt-0" <?= $currentTheme === 'tropical-modern' ? 'checked' : '' ?>>
                            </div>
                            <p class="small mb-3" style="color: #475569; font-size: 0.82rem; line-height: 1.45;">
                                Alami, hijau asri, hangat, lengkungan organik, keluarga, arsitektur tropis modern berkelanjutan.
                            </p>
                            <div class="d-flex gap-1.5 align-items-center flex-wrap">
                                <span class="badge" style="background: #1b4332; color: #fff;">#1b4332 Forest</span>
                                <span class="badge" style="background: #bc6c25; color: #fff;">#bc6c25 Earth</span>
                                <span class="badge" style="background: #e9edc9; color: #1b4332;">Organic 20px</span>
                            </div>
                        </label>
                    </div>

                    <!-- Theme 5: Urban Contemporary -->
                    <div class="col-md-6">
                        <label class="d-block h-100 p-3 rounded-3 border <?= $currentTheme === 'urban-contemporary' ? 'border-info bg-info-subtle' : 'bg-white' ?>" style="cursor: pointer; transition: all 0.2s ease; border-width: 2px !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-buildings-fill text-info fs-5"></i> Urban Contemporary
                                </span>
                                <input type="radio" name="site_theme_preset" value="urban-contemporary" class="form-check-input mt-0" <?= $currentTheme === 'urban-contemporary' ? 'checked' : '' ?>>
                            </div>
                            <p class="small mb-3" style="color: #475569; font-size: 0.82rem; line-height: 1.45;">
                                Modern metropolitan, garis geometris tegas, tipografi kuat, kontras tinggi, foto arsitektur modern.
                            </p>
                            <div class="d-flex gap-1.5 align-items-center flex-wrap">
                                <span class="badge" style="background: #0284c7; color: #fff;">#0284c7 Electric</span>
                                <span class="badge" style="background: #0f172a; color: #fff;">#0f172a Graphite</span>
                                <span class="badge" style="background: #f1f5f9; color: #0284c7;">Geometric 10px</span>
                            </div>
                        </label>
                    </div>

                    <!-- Theme 6: Editorial Architecture -->
                    <div class="col-md-6">
                        <label class="d-block h-100 p-3 border <?= $currentTheme === 'editorial-architecture' ? 'border-dark bg-light' : 'bg-white' ?>" style="cursor: pointer; transition: all 0.2s ease; border: 2px solid #171717 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-journal-text text-dark fs-5"></i> Editorial Architecture
                                </span>
                                <input type="radio" name="site_theme_preset" value="editorial-architecture" class="form-check-input mt-0" <?= $currentTheme === 'editorial-architecture' ? 'checked' : '' ?>>
                            </div>
                            <p class="small mb-3" style="color: #475569; font-size: 0.82rem; line-height: 1.45;">
                                Majalah arsitektur papan atas, grid asimetris, tipografi besar, section numbering (01, 02), storytelling caption.
                            </p>
                            <div class="d-flex gap-1.5 align-items-center flex-wrap">
                                <span class="badge" style="background: #171717; color: #fff;">#171717 Ink</span>
                                <span class="badge" style="background: #e11d48; color: #fff;">#e11d48 Rose</span>
                                <span class="badge" style="background: #ffffff; color: #171717; border: 1px solid #171717;">0px Editorial</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Preview langsung tema di halaman utama:</span>
                    <a href="<?= site_url('/?theme=' . $currentTheme) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-eye me-1"></i> Buka Preview Tema
                    </a>
                </div>
            </div>

            <input type="hidden" name="primary_color" id="primaryColorText" value="<?= old('primary_color', $settings['primary_color'] ?? '#1e3a8a') ?>">
            <input type="hidden" name="secondary_color" id="secondaryColorText" value="<?= old('secondary_color', $settings['secondary_color'] ?? '#0d9488') ?>">
            <input type="hidden" name="accent_color" id="accentColorText" value="<?= old('accent_color', $settings['accent_color'] ?? '#f59e0b') ?>">
            <input type="hidden" name="gradient_from" id="gradFromText" value="<?= old('gradient_from', $settings['gradient_from'] ?? ($settings['primary_color'] ?? '#1e3a8a')) ?>">
            <input type="hidden" name="gradient_to" id="gradToText" value="<?= old('gradient_to', $settings['gradient_to'] ?? ($settings['secondary_color'] ?? '#0d9488')) ?>">
            <input type="hidden" name="gradient_angle" id="gradAngleSelect" value="<?= old('gradient_angle', $settings['gradient_angle'] ?? '135deg') ?>">
            <input type="hidden" name="heading_font" value="<?= old('heading_font', $settings['heading_font'] ?? 'default') ?>">
            <input type="hidden" name="body_font" value="<?= old('body_font', $settings['body_font'] ?? 'default') ?>">
        </div>

        <!-- Right: Promo Modal & VA & Global Brochure -->
        <div class="col-lg-6">
            <!-- Promo Modal Pop-up -->
            <div class="card p-4 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-megaphone text-danger me-2"></i>Promo Pop-up Modal (Beranda)</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="promoToggle" name="promo_modal_active" value="1" <?= ($settings['promo_modal_active'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold small" for="promoToggle">Aktifkan Pop-up</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Judul Banner Promo</label>
                    <input type="text" name="promo_modal_title" class="form-control" value="<?= old('promo_modal_title', $settings['promo_modal_title'] ?? '') ?>" placeholder="Contoh: Promo Launching Cluster Lavender">
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi / Detail Promo</label>
                    <textarea name="promo_modal_desc" class="form-control" rows="2"><?= old('promo_modal_desc', $settings['promo_modal_desc'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teks Tombol Aksi</label>
                    <input type="text" name="promo_modal_btn_text" class="form-control" value="<?= old('promo_modal_btn_text', $settings['promo_modal_btn_text'] ?? 'Klaim Promo Sekarang via WA') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Gambar Banner Promo (Pop-up)</label>
                    <p class="small mb-2" style="color: #64748b; font-size: 0.78rem;">
                        Ditampilkan di pop-up modal saat pengunjung pertama kali membuka beranda.
                    </p>

                    <?php
                    $currPromo = $settings['promo_modal_image'] ?? '';
                    ?>
                    <div class="d-flex flex-wrap align-items-center gap-3 p-3 bg-white rounded-3 border mb-2">
                        <div class="bg-light p-1 rounded border d-flex align-items-center justify-content-center overflow-hidden" style="width: 140px; height: 85px;">
                            <img id="promoPreviewImg" src="<?= esc($currPromo) ?>" alt="Banner Promo" class="img-fluid <?= empty($currPromo) ? 'd-none' : '' ?>" style="max-height: 80px; width: 100%; object-fit: cover;">
                            <span id="promoPlaceholderBox" class="text-muted small text-center <?= !empty($currPromo) ? 'd-none' : '' ?>">
                                <i class="bi bi-card-image fs-3 d-block text-secondary opacity-50"></i>
                                Tanpa banner
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                                <input type="text" name="promo_modal_image_url" id="promo_modal_image_url" class="form-control" placeholder="https://... atau pilih dari File Manager" value="<?= old('promo_modal_image', $currPromo) ?>">
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-primary" onclick="openMediaPicker('promo_modal_image_url', 'promoPreviewImg')">
                                    <i class="bi bi-folder2-open me-1"></i> Pilih dari File Manager
                                </button>
                                <label class="btn btn-sm btn-outline-secondary mb-0" style="cursor: pointer;">
                                    <i class="bi bi-upload me-1"></i> Unggah Gambar
                                    <input type="file" name="promo_image_file" id="promoImageInput" class="d-none" accept="image/*">
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-danger <?= empty($currPromo) ? 'd-none' : '' ?>" id="btnClearPromo" onclick="clearPromoSelection()">
                                    <i class="bi bi-trash me-1"></i> Hapus Gambar
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="checkbox" name="delete_promo_image" id="deletePromoCheck" value="1" class="d-none">
                </div>
            </div>

            <!-- Virtual Assistant Widget & n8n AI Chatbot Integration -->
            <div class="card p-4 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-robot text-primary me-2"></i>Asisten Virtual &amp; AI Chatbot (n8n)</h5>
                    <span class="badge bg-danger px-3 py-1 rounded-pill"><i class="bi bi-diagram-3-fill me-1"></i> Powered by n8n Workflow</span>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Asisten Virtual</label>
                        <input type="text" name="va_name" class="form-control" value="<?= old('va_name', $settings['va_name'] ?? 'Sarah - Konsultan Properti') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor WhatsApp VA</label>
                        <input type="text" name="va_phone" class="form-control" value="<?= old('va_phone', $settings['va_phone'] ?? '6281234567890') ?>" placeholder="6281234567890">
                    </div>
                </div>

                <!-- n8n Webhook Configuration Box -->
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label text-dark mb-0"><i class="bi bi-link-45deg text-danger me-1"></i> n8n Webhook URL Chatbot</label>
                        <span class="badge bg-danger text-white" style="font-size: 0.7rem;">n8n Cloud / Self-Hosted</span>
                    </div>
                    <input type="url" name="n8n_webhook_url" class="form-control font-monospace small mb-2" placeholder="https://ercy.app.n8n.cloud/webhook/.../chat" value="<?= old('n8n_webhook_url', $settings['n8n_webhook_url'] ?? 'https://ercy.app.n8n.cloud/webhook/a3da6735-771c-435f-b4c8-c9ae179329f0/chat') ?>" required>
                    <small class="d-block" style="color: #64748b; font-size: 0.75rem;">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Seluruh pesan chat pengunjung dikirimkan secara otomatis ke webhook n8n dengan data: <code>{"chatInput": "...", "sessionId": "...", "user_name": "..."}</code>.
                        <br><strong class="text-danger">Penting:</strong> Pastikan toggle workflow di editor n8n berada dalam status <strong>Active</strong>.
                    </small>
                </div>
            </div>



            <!-- Global Brochure PDF (Fallback) -->
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Brosur Global Katalog Perumahan</h5>
                <p class="small" style="color: #64748b;">File PDF ini dijadikan fallback download otomatis jika calon pembeli mengunduh brosur pada unit yang belum memiliki brosur PDF spesifik.</p>
                
                <?php 
                $currBrochure = $settings['global_brochure_pdf'] ?? '';
                $brochureUrl = '';
                if (!empty($currBrochure)) {
                    if (str_starts_with($currBrochure, 'http://') || str_starts_with($currBrochure, 'https://')) {
                        $brochureUrl = $currBrochure;
                    } elseif (file_exists(FCPATH . 'uploads/brochures/' . $currBrochure)) {
                        $brochureUrl = base_url('uploads/brochures/' . $currBrochure);
                    } elseif (file_exists(FCPATH . ltrim($currBrochure, '/'))) {
                        $brochureUrl = base_url(ltrim($currBrochure, '/'));
                    }
                }
                ?>

                <div class="p-3 bg-light rounded-3 border mb-2">
                    <div id="brochureInfoBox" class="alert alert-info py-2 px-3 small d-flex align-items-center justify-content-between mb-2 <?= empty($currBrochure) ? 'd-none' : '' ?>">
                        <span class="text-truncate fw-medium">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5 me-1 align-middle"></i>
                            <span id="brochureFilenameDisplay"><?= esc(basename($currBrochure)) ?></span>
                        </span>
                        <a id="brochureCheckLink" href="<?= esc($brochureUrl) ?>" target="_blank" class="fw-bold text-decoration-none ms-2 text-nowrap">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Unduh / Cek File
                        </a>
                    </div>
                    <div id="brochureEmptyBox" class="text-muted small py-2 mb-2 <?= !empty($currBrochure) ? 'd-none' : '' ?>">
                        <i class="bi bi-info-circle me-1"></i> Belum ada brosur PDF global yang dipilih.
                    </div>

                    <input type="hidden" name="global_brochure_pdf_url" id="global_brochure_pdf_url" value="">
                    <input type="checkbox" name="delete_global_brochure_pdf" id="deleteBrochureCheck" value="1" class="d-none">

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary" onclick="openMediaPicker('global_brochure_pdf_url')">
                            <i class="bi bi-folder2-open me-1"></i> Pilih PDF dari File Manager
                        </button>
                        <label class="btn btn-sm btn-outline-secondary mb-0" style="cursor: pointer;">
                            <i class="bi bi-upload me-1"></i> Unggah PDF Baru (.pdf, max 10MB)
                            <input type="file" name="global_brochure_pdf" id="globalBrochureInput" class="d-none" accept=".pdf,application/pdf">
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-danger <?= empty($currBrochure) ? 'd-none' : '' ?>" id="btnClearBrochure" onclick="clearBrochureSelection()">
                            <i class="bi bi-trash me-1"></i> Hapus Brosur
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Seluruh Pengaturan Web & Refresh Cache
                </button>
            </div>
        </div>
    </div>
</form>

<!-- MEDIA PICKER MODAL -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 92%;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                <ul class="nav nav-pills card-header-pills" id="mediaPickerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn-sm px-3 py-1 fw-semibold" id="tab-elfinder-btn" data-bs-toggle="tab" data-bs-target="#tab-elfinder" type="button" role="tab">
                            <i class="bi bi-folder2-open text-primary me-1"></i> File Manager
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm px-3 py-1 fw-semibold" id="tab-grid-btn" data-bs-toggle="tab" data-bs-target="#tab-grid" type="button" role="tab" onclick="loadMediaLibrary()">
                            <i class="bi bi-grid text-secondary me-1"></i> Galeri Cepat
                        </button>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openElfinderNewWindow()" title="Buka di Jendela Baru">
                        <i class="bi bi-box-arrow-up-right"></i> Jendela Baru
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="tab-content" id="mediaPickerTabContent">
                    <!-- Tab File Manager -->
                    <div class="tab-pane fade show active" id="tab-elfinder" role="tabpanel">
                        <iframe id="elfinderPickerIframe" src="" style="width: 100%; height: 580px; border: none; display: block;"></iframe>
                    </div>

                    <!-- Tab Galeri Cepat -->
                    <div class="tab-pane fade p-3" id="tab-grid" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="small text-muted">Klik pada salah satu media untuk memilihnya.</div>
                            <a href="<?= site_url('admin/media') ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-upload me-1"></i> Buka File Manager Penuh
                            </a>
                        </div>
                        <div class="row g-3 overflow-auto" id="mediaPickerGrid" style="max-height: 500px;">
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="small text-muted mt-2">Memuat media library...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let currentTargetInputId = null;
    let currentTargetPreviewId = null;

    function openMediaPicker(inputId, previewId = null) {
        currentTargetInputId = inputId;
        currentTargetPreviewId = previewId;
        const modalEl = document.getElementById('mediaPickerModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            
            // Update iframe source with target and preview parameters
            const iframe = document.getElementById('elfinderPickerIframe');
            if (iframe) {
                const popupUrl = "<?= site_url('admin/media/popup') ?>?target=" + encodeURIComponent(inputId) + (previewId ? "&preview=" + encodeURIComponent(previewId) : "");
                iframe.src = popupUrl;
            }

            modal.show();
        }
    }

    function openElfinderNewWindow() {
        const popupUrl = "<?= site_url('admin/media/popup') ?>?target=" + encodeURIComponent(currentTargetInputId || '') + (currentTargetPreviewId ? "&preview=" + encodeURIComponent(currentTargetPreviewId) : "");
        window.open(popupUrl, 'elFinderPickerWindow', 'width=1000,height=650,resizable=yes,scrollbars=no');
    }

    function closeElfinderModal() {
        const modalEl = document.getElementById('mediaPickerModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    }

    function selectMediaUrl(url) {
        if (currentTargetInputId) {
            $('#' + currentTargetInputId).val(url).trigger('input').trigger('change');
        }
        if (currentTargetPreviewId) {
            $('#' + currentTargetPreviewId).attr('src', url).removeClass('d-none');
        }

        // Logo specifics
        if (currentTargetInputId === 'company_logo_url') {
            $('#companyLogoPreviewImg').attr('src', url).removeClass('d-none');
            $('#logoPlaceholderBox').addClass('d-none');
            $('#logoFilenameDisplay').text(url.split('/').pop());
            $('#btnClearLogo').removeClass('d-none');
            $('#deleteLogoCheck').prop('checked', false);
        }

        // Promo banner specifics
        if (currentTargetInputId === 'promo_modal_image_url') {
            $('#promoPreviewImg').attr('src', url).removeClass('d-none');
            $('#promoPlaceholderBox').addClass('d-none');
            $('#btnClearPromo').removeClass('d-none');
            $('#deletePromoCheck').prop('checked', false);
        }

        // Brochure specifics
        if (currentTargetInputId === 'global_brochure_pdf_url') {
            const fname = url.split('/').pop();
            $('#brochureFilenameDisplay').text(fname);
            $('#brochureCheckLink').attr('href', url).removeClass('d-none');
            $('#brochureInfoBox').removeClass('d-none');
            $('#brochureEmptyBox').addClass('d-none');
            $('#btnClearBrochure').removeClass('d-none');
            $('#deleteBrochureCheck').prop('checked', false);
        }

        closeElfinderModal();
    }

    function clearLogoSelection() {
        $('#company_logo_url').val('');
        $('#companyLogoInput').val('');
        $('#companyLogoPreviewImg').attr('src', '').addClass('d-none');
        $('#logoPlaceholderBox').removeClass('d-none');
        $('#logoFilenameDisplay').text('Default (Teks Nama Perusahaan)');
        $('#btnClearLogo').addClass('d-none');
        $('#deleteLogoCheck').prop('checked', true);
    }

    function clearPromoSelection() {
        $('#promo_modal_image_url').val('');
        $('#promoImageInput').val('');
        $('#promoPreviewImg').attr('src', '').addClass('d-none');
        $('#promoPlaceholderBox').removeClass('d-none');
        $('#btnClearPromo').addClass('d-none');
        $('#deletePromoCheck').prop('checked', true);
    }

    function clearBrochureSelection() {
        $('#global_brochure_pdf_url').val('');
        $('#globalBrochureInput').val('');
        $('#brochureFilenameDisplay').text('');
        $('#brochureCheckLink').attr('href', '#').addClass('d-none');
        $('#brochureInfoBox').addClass('d-none');
        $('#brochureEmptyBox').removeClass('d-none');
        $('#btnClearBrochure').addClass('d-none');
        $('#deleteBrochureCheck').prop('checked', true);
    }

    // Local file upload listeners for instant preview
    $('#companyLogoInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                $('#companyLogoPreviewImg').attr('src', evt.target.result).removeClass('d-none');
                $('#logoPlaceholderBox').addClass('d-none');
                $('#logoFilenameDisplay').text(file.name + ' (Siap diunggah)');
                $('#btnClearLogo').removeClass('d-none');
                $('#deleteLogoCheck').prop('checked', false);
            };
            reader.readAsDataURL(file);
        }
    });

    $('#promoImageInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                $('#promoPreviewImg').attr('src', evt.target.result).removeClass('d-none');
                $('#promoPlaceholderBox').addClass('d-none');
                $('#promo_modal_image_url').val(file.name + ' (File Lokal)');
                $('#btnClearPromo').removeClass('d-none');
                $('#deletePromoCheck').prop('checked', false);
            };
            reader.readAsDataURL(file);
        }
    });

    $('#globalBrochureInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            $('#brochureFilenameDisplay').text(file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB - Siap diunggah)');
            $('#brochureCheckLink').addClass('d-none');
            $('#brochureInfoBox').removeClass('d-none');
            $('#brochureEmptyBox').addClass('d-none');
            $('#btnClearBrochure').removeClass('d-none');
            $('#deleteBrochureCheck').prop('checked', false);
        }
    });

    function loadMediaLibrary() {
        $('#mediaPickerGrid').html(`
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="small text-muted mt-2">Memuat media library...</p>
            </div>
        `);

        $.ajax({
            url: "<?= site_url('admin/media/list-json') ?>",
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.success && res.data.length > 0) {
                    let html = '';
                    res.data.forEach(function(item) {
                        const preview = item.is_image ? item.url : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&auto=format&fit=crop&q=80';
                        html += `
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100 border shadow-xs media-picker-card" style="cursor: pointer;" onclick="selectMediaUrl('${item.url}')">
                                    <img src="${preview}" class="card-img-top" style="height: 140px; object-fit: cover;">
                                    <div class="card-body p-2 text-truncate small fw-bold">
                                        ${item.caption || item.original_name}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#mediaPickerGrid').html(html);
                } else {
                    $('#mediaPickerGrid').html(`
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                            Belum ada media diunggah. Buka <a href="<?= site_url('admin/media') ?>" target="_blank">File Manager</a> untuk unggah berkas.
                        </div>
                    `);
                }
            },
            error: function() {
                $('#mediaPickerGrid').html('<div class="col-12 text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat media library.</div>');
            }
        });
    }

    // Preset Themes Color & Style Auto-Sync
    const presetThemes = {
        'modern-residential':    { primary: '#1e3a8a', secondary: '#0d9488', accent: '#f59e0b', heading: 'plus-jakarta-sans', body: 'plus-jakarta-sans' },
        'soft-brutalist':        { primary: '#000000', secondary: '#ff3d00', accent: '#facc15', heading: 'space-grotesk', body: 'space-grotesk' },
        'modern-luxury':         { primary: '#0f172a', secondary: '#c59b27', accent: '#c59b27', heading: 'playfair-display', body: 'plus-jakarta-sans' },
        'tropical-modern':       { primary: '#1b4332', secondary: '#bc6c25', accent: '#dda15e', heading: 'onest', body: 'onest' },
        'urban-contemporary':    { primary: '#0284c7', secondary: '#0f172a', accent: '#38bdf8', heading: 'space-grotesk', body: 'inter' },
        'editorial-architecture':{ primary: '#171717', secondary: '#e11d48', accent: '#e11d48', heading: 'space-grotesk', body: 'space-grotesk' }
    };

    $('input[name="site_theme_preset"]').on('change', function() {
        const key = $(this).val();
        if (presetThemes[key]) {
            const p = presetThemes[key];
            $('#primaryColorText').val(p.primary);
            $('#secondaryColorText').val(p.secondary);
            $('#accentColorText').val(p.accent);
            $('#gradFromText').val(p.primary);
            $('#gradToText').val(p.secondary);
            $('input[name="heading_font"]').val(p.heading);
            $('input[name="body_font"]').val(p.body);

            // Update border style of cards
            $('input[name="site_theme_preset"]').closest('label').removeClass('border-primary bg-primary-subtle border-dark bg-secondary-subtle border-warning bg-warning-subtle border-success bg-success-subtle border-info bg-info-subtle');
            $(this).closest('label').addClass('border-primary bg-primary-subtle');
        }
    });

    // Sync localStorage theme preview when saved
    $('form').on('submit', function() {
        const selectedTheme = $('input[name="site_theme_preset"]:checked').val();
        if (selectedTheme) {
            localStorage.setItem('site_theme_preset_preview', selectedTheme);
        }
    });
</script>
<?= $this->endSection() ?>
