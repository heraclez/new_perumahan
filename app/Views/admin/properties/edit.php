<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">Edit Tipe Properti: <?= esc($property['title']) ?></h5>
        <div class="small" style="color: #64748b;">Perbarui spesifikasi, harga, status, atau kelola galeri foto unit</div>
    </div>
    <a href="<?= site_url('admin/properties') ?>" class="btn btn-sm btn-outline-secondary px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<form action="<?= site_url('admin/properties/update/' . $property['id']) ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Left: Basic Info & Specs -->
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Utama</h5>
                
                <div class="mb-3">
                    <label class="form-label">Nama / Tipe Properti <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="propTitle" class="form-control" value="<?= old('title', $property['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug URL <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-dark fw-medium">/properti/</span>
                        <input type="text" name="slug" id="propSlug" class="form-control" value="<?= old('slug', $property['slug']) ?>" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Harga Properti (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-dark fw-bold">Rp</span>
                            <input type="number" name="harga" class="form-control fw-bold text-dark" value="<?= old('harga', $property['harga']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status Ketersediaan</label>
                        <div class="p-2 px-3 bg-light rounded border d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge <?= $property['status_badge'] ?? 'bg-success' ?> rounded-pill px-3 py-1.5">
                                    <?= esc($property['status_label'] ?? $property['status']) ?>
                                </span>
                                <?php if (($property['total_kavling'] ?? 0) > 0): ?>
                                    <div class="small fw-medium mt-1" style="color: #475569; font-size: 0.72rem;">
                                        <?= esc($property['available_kavling'] ?? 0) ?> Tersedia / <?= esc($property['booking_kavling'] ?? 0) ?> Booking / <?= esc($property['sold_kavling'] ?? 0) ?> Terjual
                                    </div>
                                <?php endif; ?>
                            </div>
                            <a href="<?= site_url('admin/siteplan') ?>" class="small text-primary text-decoration-none fw-bold">
                                <i class="bi bi-pin-map"></i> Siteplan
                            </a>
                        </div>
                        <input type="hidden" name="status" value="<?= esc($property['computed_status'] ?? $property['status']) ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Luas Tanah (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="luas_tanah" class="form-control" value="<?= old('luas_tanah', $property['luas_tanah']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Luas Bangunan (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="luas_bangunan" class="form-control" value="<?= old('luas_bangunan', $property['luas_bangunan']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Spesifikasi Kamar & Ruangan</label>
                    <input type="text" name="spesifikasi_kamar" class="form-control" value="<?= old('spesifikasi_kamar', $property['spesifikasi_kamar']) ?>">
                </div>

                <div class="mb-0">
                    <label class="form-label">Deskripsi Lengkap Properti</label>
                    <textarea name="deskripsi" class="form-control" rows="6"><?= old('deskripsi', $property['deskripsi']) ?></textarea>
                </div>
            </div>

            <!-- Promo Khusus Unit Card -->
            <div class="card p-4 shadow-sm mb-4 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-tag-fill text-warning me-2"></i>Promo Khusus Unit</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_promo" value="1" id="switchPromoEdit" <?= old('is_promo', $property['is_promo'] ?? 0) ? 'checked' : '' ?> style="width: 2.5em; height: 1.3em;">
                    </div>
                </div>
                <p class="small mb-3" style="color: #64748b;">Jika dicentang aktif, unit ini akan memiliki badge <strong>PROMO</strong> mencolok di katalog & memunculkan pop-up modal rincian promo saat diklik pengunjung.</p>

                <div id="promoDetailsSectionEdit" style="<?= old('is_promo', $property['is_promo'] ?? 0) ? '' : 'display: none;' ?>">
                    <div class="mb-3">
                        <label class="form-label">Judul / Headline Promo Unit</label>
                        <input type="text" name="promo_title" class="form-control" placeholder="Contoh: Promo Launching - DP 0% & Free BPHTB" value="<?= old('promo_title', $property['promo_title'] ?? '') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Rincian Keuntungan Promo (Ditampilkan di Pop-Up Modal)</label>
                        <textarea name="promo_desc" class="form-control" rows="3" placeholder="Contoh: Free Biaya AJB, BPHTB, dan Notaris senilai 30 Juta, Subsidi Angsuran KPR 1 Juta/bulan selama 1 tahun, Bonus Smart Door Lock & 2 Unit AC"><?= old('promo_desc', $property['promo_desc'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Manage Existing Gallery & Uploads -->
        <div class="col-lg-4">
            <!-- Existing Gallery Manager -->
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-images text-primary me-2"></i>Foto Galeri Saat Ini</h5>
                
                <?php if (empty($property['images'])): ?>
                    <p class="small" style="color: #64748b;">Belum ada foto galeri.</p>
                <?php else: ?>
                    <div class="row g-2 mb-3">
                        <?php foreach ($property['images'] as $img): ?>
                            <?php 
                            $imgSrc = str_starts_with($img['image_name'], 'http') ? $img['image_name'] : base_url('uploads/properties/' . $img['image_name']);
                            ?>
                            <div class="col-6 position-relative image-card-box">
                                <div class="border rounded-3 overflow-hidden position-relative">
                                    <img src="<?= esc($imgSrc) ?>" class="w-100 object-fit-cover" style="height: 90px;" alt="Gallery">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-1 rounded-circle btn-delete-image" data-id="<?= esc($img['id']) ?>" title="Hapus Foto">
                                        <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <label class="form-label">Tambah Foto Baru</label>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                <small class="d-block mt-1" style="color: #64748b; font-size: 0.75rem;">* File JPG, PNG, WEBP max 2MB.</small>
            </div>

            <!-- Brochure PDF Manager -->
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Brosur PDF</h5>
                <?php if (!empty($property['brosur_pdf'])): ?>
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center justify-content-between mb-3">
                        <span class="text-truncate fw-medium" style="max-width: 170px;"><i class="bi bi-file-pdf"></i> <?= esc($property['brosur_pdf']) ?></span>
                        <a href="<?= base_url('uploads/brochures/' . $property['brosur_pdf']) ?>" target="_blank" class="fw-bold text-decoration-none">Buka</a>
                    </div>
                <?php else: ?>
                    <p class="small mb-2" style="color: #64748b;">Menggunakan Brosur Global (Default).</p>
                <?php endif; ?>

                <label class="form-label">Ganti / Unggah Brosur PDF Baru</label>
                <input type="file" name="brosur_pdf" class="form-control" accept=".pdf">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                    <i class="bi bi-save me-1"></i> Perbarui Data Properti
                </button>
                <a href="<?= site_url('admin/properties') ?>" class="btn btn-outline-secondary rounded-pill">Batal</a>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $('#switchPromoEdit').on('change', function() {
        if ($(this).is(':checked')) {
            $('#promoDetailsSectionEdit').slideDown(200);
        } else {
            $('#promoDetailsSectionEdit').slideUp(200);
        }
    });

    $('.btn-delete-image').on('click', function(e) {
        e.preventDefault();
        var imgId = $(this).data('id');
        var btn = $(this);

        if (confirm('Hapus foto ini dari galeri properti?')) {
            $.ajax({
                url: "<?= site_url('admin/properties/delete-image') ?>/" + imgId,
                type: "POST",
                data: {
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                dataType: "json",
                success: function(res) {
                    if (res.success) {
                        btn.closest('.image-card-box').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(res.message);
                    }
                },
                error: function() {
                    alert('Gagal menghapus gambar.');
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
