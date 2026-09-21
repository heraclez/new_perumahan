<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">Tambah Tipe Properti Baru</h5>
        <div class="small" style="color: #64748b;">Isi spesifikasi unit, harga, galeri foto, dan dokumen brosur</div>
    </div>
    <a href="<?= site_url('admin/properties') ?>" class="btn btn-sm btn-outline-secondary px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<form action="<?= site_url('admin/properties/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Left: Basic Info & Specs -->
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Utama</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Nama / Tipe Properti <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="propTitle" class="form-control" placeholder="Contoh: Type 45/90 - Cluster Jasmine" value="<?= old('title') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Slug URL (Otomatis dibuat jika dikosongkan)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">/properti/</span>
                        <input type="text" name="slug" id="propSlug" class="form-control" placeholder="type-45-90-cluster-jasmine" value="<?= old('slug') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Harga Properti (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="harga" class="form-control fw-bold" placeholder="650000000" value="<?= old('harga') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Status Ketersediaan</label>
                        <div class="p-2 px-3 bg-light rounded border d-flex align-items-center justify-content-between">
                            <span class="badge bg-success rounded-pill px-3 py-1.5">Tersedia (Ready Order)</span>
                            <small class="text-muted" style="font-size: 0.72rem;">* Otomatis terhitung dari stok kavling</small>
                        </div>
                        <input type="hidden" name="status" value="Tersedia">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Luas Tanah (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="luas_tanah" class="form-control" placeholder="90" value="<?= old('luas_tanah', 90) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Luas Bangunan (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="luas_bangunan" class="form-control" placeholder="45" value="<?= old('luas_bangunan', 45) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Spesifikasi Kamar & Ruangan</label>
                    <input type="text" name="spesifikasi_kamar" class="form-control" placeholder="Contoh: 3 Kamar Tidur, 2 Kamar Mandi, 1 Carport" value="<?= old('spesifikasi_kamar') ?>">
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold small text-muted">Deskripsi Lengkap Properti</label>
                    <textarea name="deskripsi" class="form-control" rows="6" placeholder="Jelaskan keunggulan unit, spesifikasi material bangunan, fasilitas sekitar, dan promo yang berlaku..."><?= old('deskripsi') ?></textarea>
                </div>
            </div>

            <!-- Promo Khusus Unit Card -->
            <div class="card p-4 shadow-sm mb-4 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-tag-fill text-warning me-2"></i>Promo Khusus Unit</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_promo" value="1" id="switchPromo" <?= old('is_promo') ? 'checked' : '' ?> style="width: 2.5em; height: 1.3em;">
                    </div>
                </div>
                <p class="small mb-3" style="color: #64748b;">Jika dicentang aktif, unit ini akan memiliki badge <strong>PROMO</strong> mencolok di katalog & memunculkan pop-up modal rincian promo saat diklik pengunjung.</p>

                <div id="promoDetailsSection" style="<?= old('is_promo') ? '' : 'display: none;' ?>">
                    <div class="mb-3">
                        <label class="form-label">Judul / Headline Promo Unit</label>
                        <input type="text" name="promo_title" class="form-control" placeholder="Contoh: Promo Launching - DP 0% & Free BPHTB" value="<?= old('promo_title') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Rincian Keuntungan Promo (Ditampilkan di Pop-Up Modal)</label>
                        <textarea name="promo_desc" class="form-control" rows="3" placeholder="Contoh: Free Biaya AJB, BPHTB, dan Notaris senilai 30 Juta, Subsidi Angsuran KPR 1 Juta/bulan selama 1 tahun, Bonus Smart Door Lock & 2 Unit AC"><?= old('promo_desc') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Upload Files -->
        <div class="col-lg-4">
            <!-- Upload Galeri Foto -->
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-images text-primary me-2"></i>Galeri Foto Unit</h5>
                <p class="small" style="color: #64748b;">Pilih satu atau beberapa foto sekaligus (Format JPG, PNG, WEBP, Maksimal 2MB per gambar).</p>
                
                <div class="mb-3">
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>
                <small class="d-block" style="color: #64748b;">* Foto urutan pertama akan otomatis dijadikan gambar thumbnail utama.</small>
            </div>

            <!-- Upload Brosur PDF Spesifik -->
            <div class="card p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Brosur PDF Spesifik</h5>
                <p class="small" style="color: #64748b;">Unggah brosur khusus tipe ini jika ada. Jika kosong, sistem otomatis mengunduh brosur global perusahaan.</p>
                
                <div class="mb-3">
                    <input type="file" name="brosur_pdf" class="form-control" accept=".pdf">
                </div>
                <small class="d-block" style="color: #64748b;">* Maksimal ukuran file 5 MB (Format .PDF).</small>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                    <i class="bi bi-save me-1"></i> Simpan Tipe Properti
                </button>
                <a href="<?= site_url('admin/properties') ?>" class="btn btn-outline-secondary rounded-pill">Batal</a>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $('#propTitle').on('input', function() {
        var title = $(this).val();
        var slug = title.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#propSlug').val(slug);
    });

    $('#switchPromo').on('change', function() {
        if ($(this).is(':checked')) {
            $('#promoDetailsSection').slideDown(200);
        } else {
            $('#promoDetailsSection').slideUp(200);
        }
    });
</script>
<?= $this->endSection() ?>
