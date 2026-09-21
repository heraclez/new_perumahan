<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">Master Siteplan & Denah Interaktif</h5>
        <div class="small" style="color: #64748b;">Kelola denah perumahan dan atur penempatan pin nomor kavling secara visual</div>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-3.5" data-bs-toggle="modal" data-bs-target="#uploadSiteplanModal">
        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Siteplan Baru
    </button>
</div>

<!-- Info Guidance Banner -->
<div class="card shadow-sm border-0 mb-4" style="background-color: #f0fdf4; border-left: 4px solid #16a34a !important;">
    <div class="p-3 d-flex align-items-center gap-3">
        <div class="bg-success text-white p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
            <i class="bi bi-lightbulb-fill fs-5"></i>
        </div>
        <div class="small text-dark">
            <strong>Tips Multi-Siteplan:</strong> Anda dapat mengaktifkan lebih dari satu denah siteplan sekaligus (misal: Tahap 1, Tahap 2). Jika ada <strong>2 atau lebih</strong> siteplan aktif, menu navigasi frontend otomatis berubah menjadi <strong>Menu Dropdown</strong> interaktif.
        </div>
    </div>
</div>

<!-- Siteplans List Cards -->
<div class="row g-4 mb-4">
    <?php if (empty($siteplans)): ?>
        <div class="col-12 text-center py-5">
            <div class="card p-5 shadow-sm text-center">
                <i class="bi bi-map fs-1 d-block mb-3" style="color: #94a3b8;"></i>
                <h5 class="fw-bold text-dark">Belum ada gambar Master Siteplan</h5>
                <p class="small mb-3" style="color: #64748b;">Unggah gambar denah 2D/3D master perumahan Anda untuk mulai menaruh pin kavling interaktif.</p>
                <div>
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadSiteplanModal">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Siteplan Sekarang
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($siteplans as $sp): ?>
            <?php 
            $imgSrc = str_starts_with($sp['image'], 'http') ? $sp['image'] : base_url('uploads/siteplan/' . $sp['image']);
            $isActive = !empty($sp['is_active']);
            ?>
            <div class="col-lg-6">
                <div class="card shadow-sm overflow-hidden h-100 <?= $isActive ? 'border-primary' : '' ?>">
                    <div class="position-relative bg-dark" style="height: 240px;">
                        <img src="<?= esc($imgSrc) ?>" alt="<?= esc($sp['title']) ?>" class="w-100 h-100 object-fit-cover opacity-90">
                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                            <?php if ($isActive): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> Aktif di Website
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 shadow-sm">
                                    <i class="bi bi-eye-slash-fill me-1"></i> Nonaktif (Draft)
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 shadow-sm fs-7">
                                <i class="bi bi-geo-alt-fill me-1"></i> <?= esc($sp['total_pins']) ?> Kavling Terplot
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <h5 class="fw-bold text-dark mb-0"><?= esc($sp['title']) ?></h5>
                            <!-- Status Toggle Button Form -->
                            <form action="<?= site_url('admin/siteplan/toggle-status/' . $sp['id']) ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <?php if ($isActive): ?>
                                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1" title="Klik untuk nonaktifkan">
                                        <i class="bi bi-toggle-on fs-6 text-warning"></i> Nonaktifkan
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1" title="Klik untuk aktifkan">
                                        <i class="bi bi-toggle-off fs-6 text-success"></i> Aktifkan
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>

                        <p class="small mb-3 flex-grow-1" style="color: #64748b;">
                            <?= esc($sp['description'] ?: 'Peta tata letak kavling dan blok unit perumahan.') ?>
                        </p>
                        
                        <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                            <a href="<?= site_url('admin/siteplan/builder/' . $sp['id']) ?>" class="btn btn-primary rounded-pill px-3 py-2 fw-bold flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-pin-map-fill"></i> Buka Visual Pin Plotter
                            </a>
                            <a href="<?= site_url('siteplan/' . $sp['id']) ?>" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-2" title="Pratinjau Publik Halaman Ini">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Preview
                            </a>
                            <form action="<?= site_url('admin/siteplan/delete/' . $sp['id']) ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siteplan ini? Semua pin terkait akan dihapus.');" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger rounded-pill px-3 py-2" title="Hapus Siteplan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Upload Siteplan Modal -->
<div class="modal fade" id="uploadSiteplanModal" tabindex="-1" aria-labelledby="uploadSiteplanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= site_url('admin/siteplan/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="uploadSiteplanModalLabel">
                        <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Upload Master Siteplan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Judul / Nama Siteplan <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Master Plan Cluster Grand Harmoni" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Gambar Denah / Master Plan (Resolusi Tinggi) <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">* Format JPG, PNG, WEBP. Disarankan gambar denah tajam/horizontal (Maksimal 10MB).</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">Deskripsi Singkat</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan cluster, tahap pembangunan, atau fasilitas yang tertera pada denah..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-upload me-1"></i> Upload & Lanjut Plotting Pin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
