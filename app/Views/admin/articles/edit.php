<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center mb-5">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Edit Artikel</h4>
                <small class="text-muted">Perbarui konten artikel, kategori, atau metadata SEO.</small>
            </div>
            <div class="d-flex gap-2">
                <?php if ($article['status'] === 'published'): ?>
                    <a href="<?= site_url('blog/' . $article['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Website
                    </a>
                <?php endif; ?>
                <a href="<?= site_url('admin/articles') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan validasi:</div>
                <ul class="mb-0 small">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('admin/articles/update/' . $article['id']) ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Judul Artikel <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg fw-bold" value="<?= old('title', $article['title']) ?>" required>
                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                        Slug URL saat ini: <code>/blog/<?= esc($article['slug']) ?></code>
                    </small>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Kategori Artikel <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <?php $catVal = old('category', $article['category']); ?>
                            <option value="Tips KPR" <?= $catVal === 'Tips KPR' ? 'selected' : '' ?>>Tips KPR &amp; Simulasi Bank</option>
                            <option value="Edukasi Properti" <?= $catVal === 'Edukasi Properti' ? 'selected' : '' ?>>Edukasi &amp; Legalitas Properti</option>
                            <option value="Desain & Inspirasi" <?= $catVal === 'Desain & Inspirasi' ? 'selected' : '' ?>>Desain Arsitektur &amp; Inspirasi Hunian</option>
                            <option value="Progres Pembangunan" <?= $catVal === 'Progres Pembangunan' ? 'selected' : '' ?>>Update Progres Kawasan &amp; Berita</option>
                            <option value="Info Kawasan" <?= $catVal === 'Info Kawasan' ? 'selected' : '' ?>>Fasilitas &amp; Aksesibilitas Lingkungan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nama Penulis</label>
                        <input type="text" name="author_name" class="form-control" value="<?= old('author_name', $article['author_name']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Ringkasan / Excerpt</label>
                    <textarea name="excerpt" class="form-control" rows="2"><?= old('excerpt', $article['excerpt']) ?></textarea>
                </div>

                <!-- Featured Image -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <label class="form-label fw-bold small mb-2 d-flex align-items-center gap-1">
                        <i class="bi bi-image text-primary"></i> Gambar Utama (Cover Featured Image)
                    </label>
                    <div class="row g-3 align-items-center">
                        <div class="col-md-7">
                            <label class="small text-muted mb-1">Ganti dengan Unggah Foto Baru (JPG, PNG, WebP max 3MB):</label>
                            <input type="file" name="featured_image" id="featuredImgInput" class="form-control form-control-sm mb-2" accept="image/*">
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-muted">atau ganti dengan URL Gambar:</span>
                            </div>
                            <input type="url" name="featured_image_url" id="featuredImgUrl" class="form-control form-control-sm font-monospace mt-1" placeholder="https://..." value="<?= old('featured_image_url', $article['featured_image']) ?>">
                        </div>
                        <div class="col-md-5 text-center">
                            <div class="border rounded bg-white p-1 d-inline-block shadow-xs" style="max-width: 100%;">
                                <img id="coverPreview" src="<?= esc($article['featured_image'] ?: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&auto=format&fit=crop&q=80') ?>" alt="Preview" class="img-fluid rounded object-fit-cover" style="height: 120px; width: 200px;">
                            </div>
                            <small class="d-block text-muted mt-1" style="font-size: 0.72rem;">Pratinjau Cover</small>
                        </div>
                    </div>
                </div>

                <!-- Content Editor -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Isi Konten Artikel <span class="text-danger">*</span></label>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary btn-format" data-wrap="&lt;h3&gt;,&lt;/h3&gt;" title="Heading 3">H3</button>
                            <button type="button" class="btn btn-outline-secondary btn-format" data-wrap="&lt;p&gt;,&lt;/p&gt;" title="Paragraf">P</button>
                            <button type="button" class="btn btn-outline-secondary btn-format" data-wrap="&lt;strong&gt;,&lt;/strong&gt;" title="Tebal"><i class="bi bi-type-bold"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-format" data-wrap="&lt;ul&gt;&lt;li&gt;,&lt;/li&gt;&lt;/ul&gt;" title="List Poin"><i class="bi bi-list-ul"></i></button>
                        </div>
                    </div>
                    <textarea name="content" id="articleContent" class="form-control font-monospace" rows="14" required><?= old('content', $article['content']) ?></textarea>
                </div>

                <!-- SEO Card -->
                <div class="accordion mb-4" id="accordionSeo">
                    <div class="accordion-item border rounded-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2.5 px-3 bg-light fw-bold small text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeo">
                                <i class="bi bi-google text-danger me-2"></i> Pengaturan Khusus SEO &amp; Meta Google (Opsional)
                            </button>
                        </h2>
                        <div id="collapseSeo" class="accordion-collapse collapse" data-bs-parent="#accordionSeo">
                            <div class="accordion-body p-3">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Custom Meta Title Google</label>
                                    <input type="text" name="meta_title" class="form-control form-control-sm" value="<?= old('meta_title', $article['meta_title']) ?>">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold">Custom Meta Description Google</label>
                                    <textarea name="meta_description" class="form-control form-control-sm" rows="2"><?= old('meta_description', $article['meta_description']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status & Publish Action -->
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pt-3 border-top">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold small">Status Publikasi:</label>
                        <select name="status" class="form-select form-select-sm" style="max-width: 160px;">
                            <option value="published" <?= old('status', $article['status']) === 'published' ? 'selected' : '' ?>>Published (Tayang)</option>
                            <option value="draft" <?= old('status', $article['status']) === 'draft' ? 'selected' : '' ?>>Draft (Konsep)</option>
                        </select>
                        <span class="badge bg-light text-secondary border ms-2 small">
                            <i class="bi bi-eye me-1"></i><?= number_format($article['views_count'] ?? 0) ?> views
                        </span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= site_url('admin/articles') ?>" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $('#featuredImgInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $('#coverPreview').attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    $('#featuredImgUrl').on('input', function() {
        const val = $(this).val().trim();
        if (val) {
            $('#coverPreview').attr('src', val);
        }
    });

    $('.btn-format').on('click', function() {
        const wrap = $(this).data('wrap').split(',');
        const textarea = document.getElementById('articleContent');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selText = textarea.value.substring(start, end);
        const replacement = wrap[0] + (selText || 'Teks di sini') + wrap[1];
        
        textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
        textarea.focus();
    });
</script>
<?= $this->endSection() ?>
