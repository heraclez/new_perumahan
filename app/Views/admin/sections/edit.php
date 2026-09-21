<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="<?= site_url('admin/sections') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Section Manager
            </a>
            <span class="badge bg-primary-subtle text-primary border px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.8rem;">
                Key: <?= esc($section['section_key']) ?>
            </span>
        </div>

        <form action="<?= site_url('admin/sections/update/' . $section['section_key']) ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Main Section Settings Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-sliders text-primary me-2"></i>Konfigurasi Utama Section
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Judul Section <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?= old('title', $section['title']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status Tampilan</label>
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" name="is_active" value="1" <?= $section['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="statusSwitch">
                                    <?= $section['is_active'] ? 'Aktif (Ditampilkan)' : 'Nonaktif (Disembunyikan)' ?>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold">Sub-judul / Tagline Section</label>
                            <input type="text" name="subtitle" class="form-control" value="<?= old('subtitle', $section['subtitle']) ?>" placeholder="Contoh: Pilihan Unit Terbaik">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Variasi Tata Letak (Layout Variant)</label>
                            <select name="layout_variant" class="form-select font-monospace">
                                <?php if ($section['section_key'] === 'hero'): ?>
                                    <option value="split" <?= $section['layout_variant'] === 'split' ? 'selected' : '' ?>>Split 2-Kolom (Kiri Teks, Kanan Media)</option>
                                    <option value="full_image" <?= $section['layout_variant'] === 'full_image' ? 'selected' : '' ?>>Full Image Background + Overlay</option>
                                    <option value="centered" <?= $section['layout_variant'] === 'centered' ? 'selected' : '' ?>>Centered Minimalist</option>
                                    <option value="editorial" <?= $section['layout_variant'] === 'editorial' ? 'selected' : '' ?>>Editorial Architecture (Bold & Asymmetric)</option>
                                    <option value="video_hero" <?= $section['layout_variant'] === 'video_hero' ? 'selected' : '' ?>>Video Background Hero</option>
                                <?php elseif ($section['section_key'] === 'properties'): ?>
                                    <option value="3_col" <?= $section['layout_variant'] === '3_col' ? 'selected' : '' ?>>3 Kolom Grid (Standar)</option>
                                    <option value="2_col" <?= $section['layout_variant'] === '2_col' ? 'selected' : '' ?>>2 Kolom Grid (Foto Lebih Besar)</option>
                                    <option value="4_col" <?= $section['layout_variant'] === '4_col' ? 'selected' : '' ?>>4 Kolom Grid (Kompak)</option>
                                    <option value="featured_grid" <?= $section['layout_variant'] === 'featured_grid' ? 'selected' : '' ?>>1 Featured Besar + Grid Kecil</option>
                                <?php elseif ($section['section_key'] === 'gallery'): ?>
                                    <option value="masonry" <?= $section['layout_variant'] === 'masonry' ? 'selected' : '' ?>>Masonry Grid Dinamis</option>
                                    <option value="grid" <?= $section['layout_variant'] === 'grid' ? 'selected' : '' ?>>Grid Rata (3 Kolom)</option>
                                    <option value="large_small" <?= $section['layout_variant'] === 'large_small' ? 'selected' : '' ?>>Large Focus + Small Highlights</option>
                                <?php elseif ($section['section_key'] === 'cta'): ?>
                                    <option value="center" <?= $section['layout_variant'] === 'center' ? 'selected' : '' ?>>Center Boxed Banner</option>
                                    <option value="split_image" <?= $section['layout_variant'] === 'split_image' ? 'selected' : '' ?>>Split 2-Kolom Image</option>
                                    <option value="bg_image" <?= $section['layout_variant'] === 'bg_image' ? 'selected' : '' ?>>Full Background Image Overlay</option>
                                <?php elseif ($section['section_key'] === 'about'): ?>
                                    <option value="split" <?= $section['layout_variant'] === 'split' ? 'selected' : '' ?>>Split 2-Kolom (Foto + Teks Cerita)</option>
                                    <option value="centered" <?= $section['layout_variant'] === 'centered' ? 'selected' : '' ?>>Centered Storyline</option>
                                    <option value="editorial" <?= $section['layout_variant'] === 'editorial' ? 'selected' : '' ?>>Editorial Storytelling</option>
                                <?php else: ?>
                                    <option value="default" <?= $section['layout_variant'] === 'default' ? 'selected' : '' ?>>Default</option>
                                    <option value="boxed" <?= $section['layout_variant'] === 'boxed' ? 'selected' : '' ?>>Boxed Card</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $styling = $content['styling'] ?? [];
            $secBgType = $styling['bg_type'] ?? 'default';
            $secTextMode = $styling['text_mode'] ?? 'auto';
            $secFontFamily = $styling['font_family'] ?? 'default';
            $secFontWeight = $styling['title_font_weight'] ?? 'default';
            $secTransform  = $styling['title_transform'] ?? 'default';
            $secTextShadow = $styling['text_shadow'] ?? 'none';
            $secOverlay    = $styling['overlay_opacity'] ?? 'default';
            $secBorderRadius = $styling['border_radius'] ?? 'default';
            ?>
            <!-- Gaya Tipografi, Warna Huruf & Latar Belakang Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-fonts text-primary me-2"></i>Kustomisasi Font, Warna Teks &amp; Latar Belakang
                    </h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">Tipografi &amp; Desain Modul</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Font Family Selection -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jenis Huruf (Font Family)</label>
                            <select name="sec_font_family" id="secFontFamilySelect" class="form-select">
                                <option value="default" <?= $secFontFamily === 'default' ? 'selected' : '' ?>>Bawaan Tema Aktif (Default)</option>
                                <option value="plus_jakarta" <?= $secFontFamily === 'plus_jakarta' ? 'selected' : '' ?>>Plus Jakarta Sans (Modern &amp; Bersih)</option>
                                <option value="space_grotesk" <?= $secFontFamily === 'space_grotesk' ? 'selected' : '' ?>>Space Grotesk (Arsitektur &amp; Bold)</option>
                                <option value="playfair" <?= $secFontFamily === 'playfair' ? 'selected' : '' ?>>Playfair Display (Mewah / Luxury Serif)</option>
                                <option value="onest" <?= $secFontFamily === 'onest' ? 'selected' : '' ?>>Onest (Elegan &amp; Lembut)</option>
                                <option value="inter" <?= $secFontFamily === 'inter' ? 'selected' : '' ?>>Inter (Netral &amp; Presisi)</option>
                                <option value="jetbrains" <?= $secFontFamily === 'jetbrains' ? 'selected' : '' ?>>JetBrains Mono (Modern Monospace)</option>
                            </select>
                            <small class="text-muted">Kustomisasi jenis huruf khusus untuk section ini.</small>
                        </div>

                        <!-- Title Font Weight -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ketebalan Judul (Font Weight)</label>
                            <select name="sec_title_font_weight" id="secFontWeightSelect" class="form-select">
                                <option value="default" <?= $secFontWeight === 'default' ? 'selected' : '' ?>>Bawaan Tema (Default)</option>
                                <option value="900" <?= $secFontWeight === '900' ? 'selected' : '' ?>>900 - Black / Extra Tebal</option>
                                <option value="700" <?= $secFontWeight === '700' ? 'selected' : '' ?>>700 - Bold / Tebal</option>
                                <option value="600" <?= $secFontWeight === '600' ? 'selected' : '' ?>>600 - Semi-Bold</option>
                                <option value="500" <?= $secFontWeight === '500' ? 'selected' : '' ?>>500 - Medium / Normal</option>
                            </select>
                            <small class="text-muted">Atur tingkat ketebalan font judul.</small>
                        </div>

                        <!-- Title Transform -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Transformasi Huruf Judul</label>
                            <select name="sec_title_transform" id="secTitleTransformSelect" class="form-select">
                                <option value="default" <?= $secTransform === 'default' ? 'selected' : '' ?>>Bawaan Tema (Default)</option>
                                <option value="uppercase" <?= $secTransform === 'uppercase' ? 'selected' : '' ?>>HURUF BESAR SEMUA (UPPERCASE)</option>
                                <option value="capitalize" <?= $secTransform === 'capitalize' ? 'selected' : '' ?>>Huruf Besar Di Awal Kata</option>
                                <option value="none" <?= $secTransform === 'none' ? 'selected' : '' ?>>Normal (Sesuai Input Teks)</option>
                            </select>
                            <small class="text-muted">Format kapitalisasi judul section.</small>
                        </div>

                        <!-- Text Color / Contrast Mode -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mode Warna Huruf (Font Color &amp; Contrast)</label>
                            <select name="sec_text_mode" id="secTextModeSelect" class="form-select">
                                <option value="auto" <?= $secTextMode === 'auto' ? 'selected' : '' ?>>Otomatis (Sesuai Background &amp; Foto)</option>
                                <option value="light" <?= $secTextMode === 'light' ? 'selected' : '' ?>>Teks Terang / Putih (#ffffff) - Cocok Foto Gelap</option>
                                <option value="dark" <?= $secTextMode === 'dark' ? 'selected' : '' ?>>Teks Gelap / Hitam (#0f172a) - Cocok Foto Terang</option>
                                <option value="custom" <?= $secTextMode === 'custom' ? 'selected' : '' ?>>Warna Font Kustom (Pilih Warna Bebas)</option>
                            </select>
                            <small class="text-muted">Pilih "Teks Terang" jika foto/latar gelap agar teks terbaca jelas.</small>
                        </div>

                        <!-- Text Shadow -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Efek Bayangan Teks (Text Shadow)</label>
                            <select name="sec_text_shadow" id="secTextShadowSelect" class="form-select">
                                <option value="none" <?= $secTextShadow === 'none' ? 'selected' : '' ?>>Tanpa Bayangan (None)</option>
                                <option value="subtle" <?= $secTextShadow === 'subtle' ? 'selected' : '' ?>>Halus (Subtle Shadow - Rekomendasi)</option>
                                <option value="strong" <?= $secTextShadow === 'strong' ? 'selected' : '' ?>>Tegas / Kuat (Strong - Sangat Kontras di Foto)</option>
                                <option value="glow" <?= $secTextShadow === 'glow' ? 'selected' : '' ?>>Pijar Gelap (Dark Ambient Glow)</option>
                            </select>
                            <small class="text-muted">Memberikan kontras tinggi agar judul terbaca di atas foto apapun.</small>
                        </div>

                        <!-- Background Type Selector -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipe Latar Belakang (Background)</label>
                            <select name="sec_bg_type" id="secBgTypeSelect" class="form-select">
                                <option value="default" <?= $secBgType === 'default' ? 'selected' : '' ?>>Otomatis Sesuai Tema (Default)</option>
                                <option value="solid" <?= $secBgType === 'solid' ? 'selected' : '' ?>>Warna Solid Kustom</option>
                                <option value="gradient" <?= $secBgType === 'gradient' ? 'selected' : '' ?>>Gradien 2 Warna (Linear Gradient)</option>
                            </select>
                            <small class="text-muted">Ganti latar belakang modul jika tanpa foto.</small>
                        </div>

                        <!-- Photo Overlay Opacity (Especially for Hero) -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Lapisan Gelap Foto Latar</label>
                            <select name="sec_overlay_opacity" id="secOverlaySelect" class="form-select">
                                <option value="default" <?= $secOverlay === 'default' ? 'selected' : '' ?>>Standar (Gelap Seimbang 75% - 84%)</option>
                                <option value="heavy" <?= $secOverlay === 'heavy' ? 'selected' : '' ?>>Sangat Gelap (85% - 92%)</option>
                                <option value="medium" <?= $secOverlay === 'medium' ? 'selected' : '' ?>>Sedang (60% - 70%)</option>
                                <option value="light" <?= $secOverlay === 'light' ? 'selected' : '' ?>>Tipis (35% - 45%)</option>
                                <option value="none" <?= $secOverlay === 'none' ? 'selected' : '' ?>>Tanpa Lapisan Gelap</option>
                            </select>
                            <small class="text-muted">Khusus layout Hero foto penuh: gelapkan foto.</small>
                        </div>

                        <!-- Component Corner Style / Border Radius -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Gaya Sudut Komponen (Corners)</label>
                            <select name="sec_border_radius" id="secBorderRadiusSelect" class="form-select">
                                <option value="default" <?= $secBorderRadius === 'default' ? 'selected' : '' ?>>Bawaan Tema Aktif (Default)</option>
                                <option value="pill" <?= $secBorderRadius === 'pill' ? 'selected' : '' ?>>Pill &amp; Rounded (18px - 9999px Lembut Ramah)</option>
                                <option value="subtle" <?= $secBorderRadius === 'subtle' ? 'selected' : '' ?>>Subtle (8px - 10px Minimalis Elegan)</option>
                                <option value="sharp" <?= $secBorderRadius === 'sharp' ? 'selected' : '' ?>>Sharp 0px (Tegas / Soft Brutalist)</option>
                            </select>
                            <small class="text-muted">Kelengkungan sudut kartu, tombol &amp; badge modul ini.</small>
                        </div>

                        <!-- Custom Font Color Options -->
                        <div class="col-12 sec-text-opt sec-text-custom <?= $secTextMode === 'custom' ? '' : 'd-none' ?>">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold small mb-2 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-palette text-primary"></i> Penyesuaian Warna Font Kustom:
                                </label>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="small text-muted mb-1 d-block">Warna Judul Utama (Title):</label>
                                        <div class="input-group mb-2">
                                            <input type="color" class="form-control form-control-color" id="secCustomTitleColorPicker" value="<?= esc($styling['custom_title_color'] ?? '#ffffff') ?>">
                                            <input type="text" name="sec_custom_title_color" id="secCustomTitleColorText" class="form-control font-monospace small" value="<?= esc($styling['custom_title_color'] ?? '#ffffff') ?>">
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap align-items-center">
                                            <span class="small text-muted me-1" style="font-size: 0.72rem;">Palet Cepat:</span>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-title-col" data-color="#ffffff" style="font-size: 0.72rem;">⚪ Putih</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-title-col" data-color="#0f172a" style="font-size: 0.72rem;">⚫ Hitam</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-title-col" data-color="#f59e0b" style="font-size: 0.72rem;">🟡 Emas</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-title-col" data-color="#10b981" style="font-size: 0.72rem;">🟢 Emerald</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-title-col" data-color="#0284c7" style="font-size: 0.72rem;">🔵 Biru Langit</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted mb-1 d-block">Warna Deskripsi / Teks Penjelas (Subtitle):</label>
                                        <div class="input-group mb-2">
                                            <input type="color" class="form-control form-control-color" id="secCustomTextColorPicker" value="<?= esc($styling['custom_text_color'] ?? '#e2e8f0') ?>">
                                            <input type="text" name="sec_custom_text_color" id="secCustomTextColorText" class="form-control font-monospace small" value="<?= esc($styling['custom_text_color'] ?? '#e2e8f0') ?>">
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap align-items-center">
                                            <span class="small text-muted me-1" style="font-size: 0.72rem;">Palet Cepat:</span>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-text-col" data-color="#f8fafc" style="font-size: 0.72rem;">⚪ Terang</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-text-col" data-color="#94a3b8" style="font-size: 0.72rem;">🔘 Abu Terang</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-text-col" data-color="#475569" style="font-size: 0.72rem;">⚫ Abu Gelap</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5 rounded-pill btn-quick-text-col" data-color="#fef08a" style="font-size: 0.72rem;">🟡 Kuning Muda</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Solid Background Options -->
                        <div class="col-12 sec-bg-opt sec-bg-solid <?= $secBgType === 'solid' ? '' : 'd-none' ?>">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold small mb-1">Pilih Warna Latar Belakang (Solid Color):</label>
                                <div class="input-group" style="max-width: 320px;">
                                    <input type="color" class="form-control form-control-color" id="secBgColorPicker" value="<?= esc($styling['bg_color'] ?? '#ffffff') ?>">
                                    <input type="text" name="sec_bg_color" id="secBgColorText" class="form-control font-monospace" value="<?= esc($styling['bg_color'] ?? '#ffffff') ?>" placeholder="#ffffff">
                                </div>
                            </div>
                        </div>

                        <!-- Gradient Background Options -->
                        <div class="col-12 sec-bg-opt sec-bg-gradient <?= $secBgType === 'gradient' ? '' : 'd-none' ?>">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold small mb-2 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-intersect text-primary"></i> Konfigurasi Gradien Section:
                                </label>
                                <div class="row g-2 align-items-center">
                                    <div class="col-sm-4">
                                        <label class="small text-muted mb-1">Warna Awal:</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="secGradFromPicker" value="<?= esc($styling['gradient_from'] ?? '#1e3a8a') ?>">
                                            <input type="text" name="sec_gradient_from" id="secGradFromText" class="form-control font-monospace small" value="<?= esc($styling['gradient_from'] ?? '#1e3a8a') ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small text-muted mb-1">Warna Akhir:</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="secGradToPicker" value="<?= esc($styling['gradient_to'] ?? '#0d9488') ?>">
                                            <input type="text" name="sec_gradient_to" id="secGradToText" class="form-control font-monospace small" value="<?= esc($styling['gradient_to'] ?? '#0d9488') ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small text-muted mb-1">Arah Sudut Gradien:</label>
                                        <select name="sec_gradient_angle" id="secGradAngle" class="form-select font-monospace small">
                                            <option value="135deg" <?= ($styling['gradient_angle'] ?? '135deg') === '135deg' ? 'selected' : '' ?>>135° (Diagonal)</option>
                                            <option value="90deg" <?= ($styling['gradient_angle'] ?? '') === '90deg' ? 'selected' : '' ?>>90° (Kiri-Kanan)</option>
                                            <option value="180deg" <?= ($styling['gradient_angle'] ?? '') === '180deg' ? 'selected' : '' ?>>180° (Atas-Bawah)</option>
                                            <option value="45deg" <?= ($styling['gradient_angle'] ?? '') === '45deg' ? 'selected' : '' ?>>45° (Menanjak)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Live Interactive Preview Box -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted mb-1 d-flex align-items-center gap-1">
                                <i class="bi bi-eye text-primary"></i> Pratinjau Tampilan Font &amp; Desain (Live Preview):
                            </label>
                            <div id="secLivePreviewBox" class="p-4 text-center shadow-sm border position-relative overflow-hidden" style="min-height: 120px; transition: all 0.3s ease; background-color: #0f172a; border-radius: 16px;">
                                <div id="secPreviewOverlay" class="position-absolute w-100 h-100 top-0 start-0" style="pointer-events: none; transition: background 0.3s ease;"></div>
                                <div class="position-relative" style="z-index: 1;">
                                    <div id="secPreviewTitle" class="fw-bold fs-4 mb-2" style="color: #ffffff; text-shadow: 0 2px 10px rgba(0,0,0,0.85);">
                                        TEMUKAN HUNIAN IMPIAN KELUARGA MODERN
                                    </div>
                                    <div id="secPreviewDesc" class="small mx-auto mb-3" style="max-width: 580px; color: rgba(255, 255, 255, 0.90); text-shadow: 0 1px 4px rgba(0,0,0,0.7);">
                                        Kawasan hunian eksklusif dengan fasilitas lengkap, aksesibilitas prima, dan lingkungan hijau asri.
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                        <span id="secPreviewBadge" class="badge bg-primary px-3 py-1.5 font-monospace" style="transition: all 0.2s ease;">Label Tagline</span>
                                        <button type="button" id="secPreviewBtn" class="btn btn-sm btn-light fw-bold px-3 py-1.5 shadow-sm" style="transition: all 0.2s ease;">
                                            <i class="bi bi-arrow-right-circle me-1"></i> Contoh Tombol
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tailored Section Details Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-pencil-square text-primary me-2"></i>Konten Spesifik (<?= strtoupper(esc($section['section_key'])) ?>)
                    </h5>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#mediaPickerModal">
                        <i class="bi bi-folder2-open me-1"></i> Buka Media Manager
                    </button>
                </div>
                <div class="card-body p-4">

                    <?php if ($section['section_key'] === 'hero'): ?>
                        <!-- ==================== HERO SECTION FIELDS ==================== -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Teks Badge Tagline Atas</label>
                                <input type="text" name="badge_text" class="form-control" value="<?= esc($content['badge_text'] ?? '') ?>" placeholder="Developer Properti Terpercaya & Legalitas Aman (SHM)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tombol Aksi 1 (Primary)</label>
                                <div class="input-group">
                                    <input type="text" name="btn1_text" class="form-control" placeholder="Teks Tombol" value="<?= esc($content['btn1_text'] ?? 'Jelajahi Tipe Rumah') ?>">
                                    <input type="text" name="btn1_link" class="form-control" placeholder="URL Target" value="<?= esc($content['btn1_link'] ?? 'properti') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tombol Aksi 2 (Secondary)</label>
                                <div class="input-group">
                                    <input type="text" name="btn2_text" class="form-control" placeholder="Teks Tombol" value="<?= esc($content['btn2_text'] ?? 'Hitung Simulasi KPR') ?>">
                                    <input type="text" name="btn2_link" class="form-control" placeholder="URL Target" value="<?= esc($content['btn2_link'] ?? 'kpr-calculator') ?>">
                                </div>
                            </div>

                            <!-- Highlights Statistics -->
                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-primary"><i class="bi bi-bar-chart-line me-1"></i> Highlight Statistik Hero</label>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label small fw-bold">Statistik 1</label>
                                    <input type="text" name="stat1_value" class="form-control form-control-sm mb-2 fw-bold" placeholder="100%" value="<?= esc($content['stat1_value'] ?? '100%') ?>">
                                    <input type="text" name="stat1_label" class="form-control form-control-sm" placeholder="Sertifikat SHM" value="<?= esc($content['stat1_label'] ?? 'Sertifikat SHM') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label small fw-bold">Statistik 2</label>
                                    <input type="text" name="stat2_value" class="form-control form-control-sm mb-2 fw-bold" placeholder="DP 0%" value="<?= esc($content['stat2_value'] ?? 'DP 0%') ?>">
                                    <input type="text" name="stat2_label" class="form-control form-control-sm" placeholder="Promo Cicilan KPR" value="<?= esc($content['stat2_label'] ?? 'Promo Cicilan KPR') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label small fw-bold">Statistik 3</label>
                                    <input type="text" name="stat3_value" class="form-control form-control-sm mb-2 fw-bold" placeholder="24/7" value="<?= esc($content['stat3_value'] ?? '24/7') ?>">
                                    <input type="text" name="stat3_label" class="form-control form-control-sm" placeholder="Keamanan & CCTV" value="<?= esc($content['stat3_label'] ?? 'Keamanan & CCTV') ?>">
                                </div>
                            </div>

                            <!-- Media Hero (Foto vs Video) -->
                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-primary"><i class="bi bi-camera-video me-1"></i> Pengaturan Media Visual Hero</label>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipe Media</label>
                                <select name="media_type" id="heroMediaType" class="form-select">
                                    <option value="image" <?= ($content['media_type'] ?? 'image') === 'image' ? 'selected' : '' ?>>Gambar / Foto Utama</option>
                                    <option value="video" <?= ($content['media_type'] ?? 'image') === 'video' ? 'selected' : '' ?>>Video (YouTube / Vimeo / MP4)</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-bold">URL Gambar / Poster Fallback</label>
                                <div class="input-group">
                                    <input type="text" name="image_url" id="heroImageUrl" class="form-control" value="<?= esc($content['image_url'] ?? '') ?>" placeholder="https://...">
                                    <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('heroImageUrl', 'heroImagePreview')">Pilih Media</button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Atau Unggah File Gambar Baru</label>
                                <input type="file" name="uploaded_image" class="form-control" accept="image/*">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Badge Harga pada Foto</label>
                                <div class="input-group">
                                    <input type="text" name="price_badge_prefix" class="form-control" placeholder="Harga Mulai Dari" value="<?= esc($content['price_badge_prefix'] ?? 'Harga Mulai Dari') ?>">
                                    <input type="text" name="price_badge_text" class="form-control fw-bold" placeholder="Rp 650 Juta-an" value="<?= esc($content['price_badge_text'] ?? 'Rp 650 Juta-an') ?>">
                                </div>
                            </div>

                            <!-- Focal Point Controls -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Focal Point Horizontal (X)</label>
                                <select name="focal_x" id="focalX" class="form-select">
                                    <option value="left" <?= ($content['focal_x'] ?? 'center') === 'left' ? 'selected' : '' ?>>LEFT (Kiri)</option>
                                    <option value="center" <?= ($content['focal_x'] ?? 'center') === 'center' ? 'selected' : '' ?>>CENTER (Tengah)</option>
                                    <option value="right" <?= ($content['focal_x'] ?? 'center') === 'right' ? 'selected' : '' ?>>RIGHT (Kanan)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Focal Point Vertikal (Y)</label>
                                <select name="focal_y" id="focalY" class="form-select">
                                    <option value="top" <?= ($content['focal_y'] ?? 'center') === 'top' ? 'selected' : '' ?>>TOP (Atas)</option>
                                    <option value="center" <?= ($content['focal_y'] ?? 'center') === 'center' ? 'selected' : '' ?>>CENTER (Tengah)</option>
                                    <option value="bottom" <?= ($content['focal_y'] ?? 'center') === 'bottom' ? 'selected' : '' ?>>BOTTOM (Bawah)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Aspect Ratio Preview</label>
                                <select name="aspect_ratio" id="aspectRatio" class="form-select">
                                    <option value="4/3" <?= ($content['aspect_ratio'] ?? '4/3') === '4/3' ? 'selected' : '' ?>>4:3 (Standar Card)</option>
                                    <option value="16/9" <?= ($content['aspect_ratio'] ?? '4/3') === '16/9' ? 'selected' : '' ?>>16:9 (Widescreen)</option>
                                    <option value="1/1" <?= ($content['aspect_ratio'] ?? '4/3') === '1/1' ? 'selected' : '' ?>>1:1 (Square)</option>
                                </select>
                            </div>

                            <!-- Live Focal Point Preview Box -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Pratinjau Non-Destructive Focal Point (CSS object-fit &amp; object-position):</label>
                                <div class="border rounded-4 overflow-hidden bg-light position-relative p-2" style="max-width: 500px;">
                                    <img id="heroImagePreview" src="<?= esc($content['image_url'] ?? 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=900&auto=format&fit=crop&q=80') ?>" alt="Preview" class="w-100 rounded-3" style="height: 250px; object-fit: cover; object-position: <?= esc(($content['focal_x'] ?? 'center') . ' ' . ($content['focal_y'] ?? 'center')) ?>;">
                                </div>
                            </div>

                            <!-- Video Options (shown if Video selected) -->
                            <div class="col-12 p-3 bg-light rounded-3 border mt-3" id="videoConfigBox">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-play-circle text-danger me-1"></i> Konfigurasi Video Hero</h6>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Sumber Video</label>
                                        <select name="video_source" class="form-select form-select-sm">
                                            <option value="youtube" <?= ($content['video_source'] ?? 'youtube') === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                                            <option value="vimeo" <?= ($content['video_source'] ?? 'youtube') === 'vimeo' ? 'selected' : '' ?>>Vimeo</option>
                                            <option value="mp4" <?= ($content['video_source'] ?? 'youtube') === 'mp4' ? 'selected' : '' ?>>Berkas MP4 Langsung</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold">URL Video / ID Embed</label>
                                        <input type="text" name="video_url" class="form-control form-control-sm" placeholder="Contoh: https://www.youtube.com/watch?v=... atau https://site.com/video.mp4" value="<?= esc($content['video_url'] ?? '') ?>">
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Background video akan diputar secara otomatis dengan atribut <code>autoplay muted loop playsinline</code>.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($section['section_key'] === 'properties'): ?>
                        <!-- ==================== PROPERTIES SECTION FIELDS ==================== -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Jumlah Unit Ditampilkan</label>
                                <input type="number" name="limit" class="form-control" value="<?= esc($content['limit'] ?? 6) ?>" min="1" max="24">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Teks Tombol Bawah</label>
                                <input type="text" name="btn_text" class="form-control" value="<?= esc($content['btn_text'] ?? 'Lihat Semua Tipe') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">URL Target Tombol</label>
                                <input type="text" name="btn_link" class="form-control" value="<?= esc($content['btn_link'] ?? 'properti') ?>">
                            </div>
                            <div class="col-12">
                                <div class="alert alert-secondary mb-0 small">
                                    <i class="bi bi-database-check text-success me-1"></i>
                                    Data properti diambil secara otomatis dari modul <strong>Katalog Properti</strong> (termasuk status ketersediaan kavling, harga, dan badge promo).
                                </div>
                            </div>
                        </div>

                    <?php elseif ($section['section_key'] === 'about'): ?>
                        <!-- ==================== ABOUT SECTION FIELDS ==================== -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Deskripsi / Narasi Perusahaan</label>
                                <textarea name="description" class="form-control" rows="4"><?= esc($content['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Poin Keunggulan 1</label>
                                <input type="text" name="highlight_1" class="form-control" value="<?= esc($content['highlight_1'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Poin Keunggulan 2</label>
                                <input type="text" name="highlight_2" class="form-control" value="<?= esc($content['highlight_2'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Poin Keunggulan 3</label>
                                <input type="text" name="highlight_3" class="form-control" value="<?= esc($content['highlight_3'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Angka Pengalaman</label>
                                <input type="text" name="experience" class="form-control" placeholder="15+ Tahun" value="<?= esc($content['experience'] ?? '15+ Tahun') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Label Pengalaman</label>
                                <input type="text" name="exp_label" class="form-control" placeholder="Pengalaman Membangun Hunian Berkualitas" value="<?= esc($content['exp_label'] ?? 'Pengalaman Membangun Hunian Berkualitas') ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">URL Foto Ilustrasi About</label>
                                <div class="input-group">
                                    <input type="text" name="image_url" id="aboutImgUrl" class="form-control" value="<?= esc($content['image_url'] ?? '') ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('aboutImgUrl')">Pilih Media</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Atau Unggah Foto Baru</label>
                                <input type="file" name="uploaded_image" class="form-control" accept="image/*">
                            </div>
                        </div>

                    <?php elseif ($section['section_key'] === 'features'): ?>
                        <!-- ==================== FEATURES / USP SECTION FIELDS ==================== -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Kalimat Pengantar (Intro)</label>
                                <input type="text" name="intro" class="form-control" value="<?= esc($content['intro'] ?? '') ?>">
                            </div>
                            <div class="col-12"><hr class="my-2"></div>
                            <?php $items = $content['items'] ?? []; ?>
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <?php $item = $items[$i] ?? ['icon' => 'bi-check-circle', 'title' => '', 'desc' => '']; ?>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <h6 class="fw-bold text-primary mb-2">Item Keunggulan #<?= $i + 1 ?></h6>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Icon Bootstrap</label>
                                            <input type="text" name="items[<?= $i ?>][icon]" class="form-control form-control-sm font-monospace" value="<?= esc($item['icon'] ?? 'bi-check-circle') ?>" placeholder="bi-shield-check">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Judul Keunggulan</label>
                                            <input type="text" name="items[<?= $i ?>][title]" class="form-control form-control-sm fw-bold" value="<?= esc($item['title'] ?? '') ?>" placeholder="Legalitas 100% Aman">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold">Keterangan Singkat</label>
                                            <textarea name="items[<?= $i ?>][desc]" class="form-control form-control-sm" rows="2"><?= esc($item['desc'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                    <?php elseif ($section['section_key'] === 'gallery'): ?>
                        <!-- ==================== GALLERY SECTION FIELDS ==================== -->
                        <div class="row g-3">
                            <div class="col-12 d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold mb-0">Daftar Foto Galeri Kawasan</label>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="btnAddGalleryItem">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Foto Baru
                                </button>
                            </div>

                            <div class="col-12" id="galleryContainer">
                                <?php $gItems = $content['items'] ?? []; ?>
                                <?php foreach ($gItems as $idx => $g): ?>
                                    <div class="p-3 bg-light rounded-3 border mb-3 gallery-row position-relative">
                                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-remove-gallery" title="Hapus Foto"></button>
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-2 text-center">
                                                <img src="<?= esc($g['image']) ?>" alt="Preview" class="rounded-3 img-fluid border bg-white" style="height: 70px; width: 100%; object-fit: cover;">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small fw-bold mb-1">URL Gambar</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" name="gallery_items[<?= $idx ?>][image]" id="gImg_<?= $idx ?>" class="form-control" value="<?= esc($g['image']) ?>">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('gImg_<?= $idx ?>')">Pilih</button>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Keterangan / Caption</label>
                                                <input type="text" name="gallery_items[<?= $idx ?>][caption]" class="form-control form-control-sm" value="<?= esc($g['caption']) ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold mb-1">Kategori / Tag</label>
                                                <input type="text" name="gallery_items[<?= $idx ?>][tag]" class="form-control form-control-sm" value="<?= esc($g['tag'] ?? 'Fasilitas') ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php elseif ($section['section_key'] === 'siteplan'): ?>
                        <!-- ==================== SITEPLAN SECTION FIELDS ==================== -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Keterangan Petunjuk Siteplan</label>
                                <textarea name="description" class="form-control" rows="2"><?= esc($content['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teks Tombol Aksi</label>
                                <input type="text" name="btn_text" class="form-control" value="<?= esc($content['btn_text'] ?? 'Buka Denah Masterplan Lengkap') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">URL Target Tombol</label>
                                <input type="text" name="btn_link" class="form-control" value="<?= esc($content['btn_link'] ?? 'siteplan') ?>">
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info small mb-0">
                                    <i class="bi bi-map text-primary me-1"></i>
                                    Denah dan koordinat interaktif diambil langsung dari modul <strong>Master Siteplan</strong> existing.
                                </div>
                            </div>
                        </div>

                    <?php elseif ($section['section_key'] === 'promo'): ?>
                        <!-- ==================== PROMO SECTION FIELDS ==================== -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Badge Penawaran</label>
                                <input type="text" name="badge" class="form-control" value="<?= esc($content['badge'] ?? 'KUOTA TERBATAS') ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Teks Tombol Klaim</label>
                                <input type="text" name="btn_text" class="form-control" value="<?= esc($content['btn_text'] ?? 'Klaim Promo via WhatsApp') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Deskripsi Promo</label>
                                <textarea name="desc" class="form-control" rows="3"><?= esc($content['desc'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">URL Gambar Banner Promo</label>
                                <div class="input-group">
                                    <input type="text" name="image_url" id="promoImgUrl" class="form-control" value="<?= esc($content['image_url'] ?? '') ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('promoImgUrl')">Pilih Media</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Atau Unggah Gambar</label>
                                <input type="file" name="uploaded_image" class="form-control" accept="image/*">
                            </div>
                        </div>

                    <?php elseif ($section['section_key'] === 'cta'): ?>
                        <!-- ==================== CTA SECTION FIELDS ==================== -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Deskripsi Ajakan Bertindak</label>
                                <textarea name="desc" class="form-control" rows="2"><?= esc($content['desc'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tombol 1 (WhatsApp)</label>
                                <input type="text" name="btn1_text" class="form-control" value="<?= esc($content['btn1_text'] ?? 'Jadwalkan Survey Lokasi') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tombol 2 (Katalog PDF)</label>
                                <div class="input-group">
                                    <input type="text" name="btn2_text" class="form-control" placeholder="Unduh E-Katalog PDF" value="<?= esc($content['btn2_text'] ?? 'Unduh E-Katalog PDF') ?>">
                                    <input type="text" name="btn2_link" class="form-control" placeholder="brochure/download-global" value="<?= esc($content['btn2_link'] ?? 'brochure/download-global') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipe Background</label>
                                <select name="bg_type" class="form-select">
                                    <option value="gradient" <?= ($content['bg_type'] ?? 'gradient') === 'gradient' ? 'selected' : '' ?>>Warna Gradasi Tema</option>
                                    <option value="image" <?= ($content['bg_type'] ?? 'gradient') === 'image' ? 'selected' : '' ?>>Foto / Gambar Latar</option>
                                    <option value="video" <?= ($content['bg_type'] ?? 'gradient') === 'video' ? 'selected' : '' ?>>Video Background (MP4)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">URL Foto Background</label>
                                <div class="input-group">
                                    <input type="text" name="bg_image" id="ctaBgImage" class="form-control" value="<?= esc($content['bg_image'] ?? '') ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('ctaBgImage')">Pilih Media</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Atau Unggah Gambar Baru</label>
                                <input type="file" name="uploaded_image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">URL Video Background (MP4)</label>
                                <input type="text" name="bg_video" class="form-control" placeholder="https://..." value="<?= esc($content['bg_video'] ?? '') ?>">
                            </div>
                        </div>

                    <?php endif; ?>

                </div>
                <div class="card-footer bg-light py-3 d-flex justify-content-between align-items-center">
                    <a href="<?= site_url('admin/sections') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Section
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

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
                    <!-- Tab elFinder -->
                    <div class="tab-pane fade show active" id="tab-elfinder" role="tabpanel">
                        <iframe id="elfinderPickerIframe" src="" style="width: 100%; height: 580px; border: none; display: block;"></iframe>
                    </div>

                    <!-- Tab Galeri Cepat -->
                    <div class="tab-pane fade p-3" id="tab-grid" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="small text-muted">Klik pada salah satu media untuk memilihnya.</div>
                            <a href="<?= site_url('admin/media') ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-upload me-1"></i> Unggah Berkas Baru
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
                        Belum ada media diunggah. Buka <a href="<?= site_url('admin/media') ?>" target="_blank">Media Manager</a> untuk unggah file.
                    </div>
                `);
            }
        },
        error: function() {
            $('#mediaPickerGrid').html('<div class="col-12 text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat media library.</div>');
        }
    });
}

function selectMediaUrl(url) {
    if (currentTargetInputId) {
        $('#' + currentTargetInputId).val(url).trigger('input').trigger('change');
    }
    if (currentTargetPreviewId) {
        $('#' + currentTargetPreviewId).attr('src', url);
    }
    closeElfinderModal();
}

// Live Focal Point Preview Updates
function updateHeroPreview() {
    const fx = $('#focalX').val() || 'center';
    const fy = $('#focalY').val() || 'center';
    const preview = $('#heroImagePreview');
    if (preview.length) {
        preview.css('object-position', `${fx} ${fy}`);
    }
}

$('#focalX, #focalY').on('change', updateHeroPreview);

$('#heroImageUrl').on('input', function() {
    const url = $(this).val().trim();
    if (url) {
        $('#heroImagePreview').attr('src', url);
    }
});

// Add Dynamic Gallery Item
let galleryIndex = <?= count($content['items'] ?? []) ?>;
$('#btnAddGalleryItem').on('click', function() {
    galleryIndex++;
    const html = `
        <div class="p-3 bg-light rounded-3 border mb-3 gallery-row position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-remove-gallery" title="Hapus Foto"></button>
            <div class="row g-2 align-items-center">
                <div class="col-md-2 text-center">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&auto=format&fit=crop&q=80" alt="Preview" class="rounded-3 img-fluid border bg-white" style="height: 70px; width: 100%; object-fit: cover;">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold mb-1">URL Gambar</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="gallery_items[${galleryIndex}][image]" id="gImg_${galleryIndex}" class="form-control" placeholder="https://...">
                        <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('gImg_${galleryIndex}')">Pilih</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Keterangan / Caption</label>
                    <input type="text" name="gallery_items[${galleryIndex}][caption]" class="form-control form-control-sm" placeholder="Contoh: Clubhouse Warga">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Kategori / Tag</label>
                    <input type="text" name="gallery_items[${galleryIndex}][tag]" class="form-control form-control-sm" value="Fasilitas">
                </div>
            </div>
        </div>
    `;
    $('#galleryContainer').append(html);
});

$(document).on('click', '.btn-remove-gallery', function() {
    $(this).closest('.gallery-row').remove();
});

// Section Styling Controls & Live Preview
function updateSecPreview() {
    const bgType = $('#secBgTypeSelect').val();
    const textMode = $('#secTextModeSelect').val();
    const fontFam = $('#secFontFamilySelect').val();
    const fontWeight = $('#secFontWeightSelect').val();
    const transform = $('#secTitleTransformSelect').val();
    const textShadow = $('#secTextShadowSelect').val();
    const overlay = $('#secOverlaySelect').val();

    const $box = $('#secLivePreviewBox');
    const $overlay = $('#secPreviewOverlay');
    const $title = $('#secPreviewTitle');
    const $desc = $('#secPreviewDesc');

    // Toggle option panels
    $('.sec-bg-opt').addClass('d-none');
    if (bgType === 'solid') $('.sec-bg-solid').removeClass('d-none');
    if (bgType === 'gradient') $('.sec-bg-gradient').removeClass('d-none');

    $('.sec-text-opt').addClass('d-none');
    if (textMode === 'custom') $('.sec-text-custom').removeClass('d-none');

    // Apply Background
    if (bgType === 'solid') {
        const col = $('#secBgColorText').val() || '#ffffff';
        $box.css('background', col);
    } else if (bgType === 'gradient') {
        const from = $('#secGradFromText').val() || '#1e3a8a';
        const to = $('#secGradToText').val() || '#0d9488';
        const angle = $('#secGradAngle').val() || '135deg';
        $box.css('background', `linear-gradient(${angle}, ${from}, ${to})`);
    } else {
        $box.css('background', '#0f172a');
    }

    // Apply Overlay Opacity
    if (overlay === 'heavy') {
        $overlay.css('background', 'rgba(0, 0, 0, 0.85)');
    } else if (overlay === 'medium') {
        $overlay.css('background', 'rgba(0, 0, 0, 0.65)');
    } else if (overlay === 'light') {
        $overlay.css('background', 'rgba(0, 0, 0, 0.40)');
    } else if (overlay === 'none') {
        $overlay.css('background', 'transparent');
    } else {
        $overlay.css('background', 'rgba(0, 0, 0, 0.75)');
    }

    // Apply Font Family
    const fontMap = {
        'plus_jakarta': "'Plus Jakarta Sans', sans-serif",
        'space_grotesk': "'Space Grotesk', sans-serif",
        'playfair': "'Playfair Display', serif",
        'onest': "'Onest', sans-serif",
        'inter': "'Inter', sans-serif",
        'jetbrains': "'JetBrains Mono', monospace",
        'default': 'inherit'
    };
    const chosenFont = fontMap[fontFam] || 'inherit';
    $title.css('font-family', chosenFont);
    $desc.css('font-family', chosenFont);

    // Apply Font Weight
    if (fontWeight && fontWeight !== 'default') {
        $title.css('font-weight', fontWeight);
    } else {
        $title.css('font-weight', '800');
    }

    // Apply Text Transform
    if (transform && transform !== 'default') {
        $title.css('text-transform', transform);
    } else {
        $title.css('text-transform', 'none');
    }

    // Apply Text Shadow
    if (textShadow === 'subtle') {
        $title.css('text-shadow', '0 2px 8px rgba(0, 0, 0, 0.65)');
        $desc.css('text-shadow', '0 1px 4px rgba(0, 0, 0, 0.50)');
    } else if (textShadow === 'strong') {
        $title.css('text-shadow', '0 4px 16px rgba(0, 0, 0, 0.95), 0 1px 3px rgba(0, 0, 0, 0.85)');
        $desc.css('text-shadow', '0 2px 8px rgba(0, 0, 0, 0.85)');
    } else if (textShadow === 'glow') {
        $title.css('text-shadow', '0 0 18px rgba(0, 0, 0, 0.95)');
        $desc.css('text-shadow', '0 0 10px rgba(0, 0, 0, 0.85)');
    } else {
        $title.css('text-shadow', 'none');
        $desc.css('text-shadow', 'none');
    }

    // Apply Text Color
    if (textMode === 'light') {
        $title.css('color', '#ffffff');
        $desc.css('color', '#e2e8f0');
    } else if (textMode === 'dark') {
        $title.css('color', '#0f172a');
        $desc.css('color', '#475569');
    } else if (textMode === 'custom') {
        $title.css('color', $('#secCustomTitleColorText').val() || '#ffffff');
        $desc.css('color', $('#secCustomTextColorText').val() || '#e2e8f0');
    } else {
        // Auto
        if (bgType === 'gradient' || bgType === 'default') {
            $title.css('color', '#ffffff');
            $desc.css('color', '#e2e8f0');
        } else {
            $title.css('color', '#0f172a');
            $desc.css('color', '#475569');
        }
    }

    // Apply Border Radius
    const borderRadius = $('#secBorderRadiusSelect').val();
    const $previewBadge = $('#secPreviewBadge');
    const $previewBtn = $('#secPreviewBtn');

    if (borderRadius === 'pill') {
        $box.css('border-radius', '28px');
        $previewBadge.css('border-radius', '9999px');
        $previewBtn.css('border-radius', '9999px');
    } else if (borderRadius === 'subtle') {
        $box.css('border-radius', '10px');
        $previewBadge.css('border-radius', '6px');
        $previewBtn.css('border-radius', '8px');
    } else if (borderRadius === 'sharp') {
        $box.css('border-radius', '0px');
        $previewBadge.css('border-radius', '0px');
        $previewBtn.css('border-radius', '0px');
    } else {
        $box.css('border-radius', '16px');
        $previewBadge.css('border-radius', '6px');
        $previewBtn.css('border-radius', '8px');
    }
}

$('#secBgTypeSelect, #secTextModeSelect, #secGradAngle, #secFontFamilySelect, #secFontWeightSelect, #secTitleTransformSelect, #secTextShadowSelect, #secOverlaySelect, #secBorderRadiusSelect').on('change', updateSecPreview);
$('#secBgColorPicker').on('input', function() { $('#secBgColorText').val($(this).val()); updateSecPreview(); });
$('#secBgColorText').on('input', function() { $('#secBgColorPicker').val($(this).val()); updateSecPreview(); });
$('#secGradFromPicker').on('input', function() { $('#secGradFromText').val($(this).val()); updateSecPreview(); });
$('#secGradFromText').on('input', function() { $('#secGradFromPicker').val($(this).val()); updateSecPreview(); });
$('#secGradToPicker').on('input', function() { $('#secGradToText').val($(this).val()); updateSecPreview(); });
$('#secGradToText').on('input', function() { $('#secGradToPicker').val($(this).val()); updateSecPreview(); });
$('#secCustomTitleColorPicker').on('input', function() { $('#secCustomTitleColorText').val($(this).val()); updateSecPreview(); });
$('#secCustomTitleColorText').on('input', function() { $('#secCustomTitleColorPicker').val($(this).val()); updateSecPreview(); });
$('#secCustomTextColorPicker').on('input', function() { $('#secCustomTextColorText').val($(this).val()); updateSecPreview(); });
$('#secCustomTextColorText').on('input', function() { $('#secCustomTextColorPicker').val($(this).val()); updateSecPreview(); });

// Quick Color Palette triggers
$('.btn-quick-title-col').on('click', function() {
    const col = $(this).data('color');
    $('#secCustomTitleColorPicker').val(col);
    $('#secCustomTitleColorText').val(col);
    updateSecPreview();
});

$('.btn-quick-text-col').on('click', function() {
    const col = $(this).data('color');
    $('#secCustomTextColorPicker').val(col);
    $('#secCustomTextColorText').val(col);
    updateSecPreview();
});

// Initialize preview on page load
updateSecPreview();
</script>
<?= $this->endSection() ?>
