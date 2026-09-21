<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Main Properties Card -->
<div class="card border-0 shadow-sm overflow-hidden">
    <!-- Card Header with Action Button & Filter Form -->
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">Daftar Unit & Tipe</h5>
                    <span class="badge bg-light text-secondary border px-2 py-1"><?= count($properties) ?> Total</span>
                </div>
                <small class="text-muted">Kelola informasi harga, spesifikasi, dan galeri foto unit.</small>
            </div>

            <div class="col-md-8 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-end gap-2">
                <!-- Search & Status Filter Form -->
                <form method="GET" action="<?= site_url('admin/properties') ?>" class="d-flex align-items-center gap-2 flex-grow-1 flex-sm-grow-0">
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control border-start-0" placeholder="Cari nama properti..." value="<?= esc($filters['keyword'] ?? '') ?>">
                    </div>

                    <select name="status" class="form-select form-select-sm" style="max-width: 170px;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Promo" <?= ($filters['status'] ?? '') === 'Promo' ? 'selected' : '' ?>>Sedang Promo</option>
                        <option value="Tersedia" <?= ($filters['status'] ?? '') === 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="Booking" <?= ($filters['status'] ?? '') === 'Booking' ? 'selected' : '' ?>>Booking</option>
                        <option value="Terjual" <?= ($filters['status'] ?? '') === 'Terjual' ? 'selected' : '' ?>>Terjual</option>
                    </select>

                    <?php if (!empty($filters['status']) || !empty($filters['keyword'])): ?>
                        <a href="<?= site_url('admin/properties') ?>" class="btn btn-sm btn-light text-secondary border" title="Reset Filter">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    <?php endif; ?>
                </form>

                <!-- Tambah Properti Button -->
                <a href="<?= site_url('admin/properties/create') ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Properti
                </a>
            </div>
        </div>
    </div>

    <!-- Table Body -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;" class="ps-4">Foto</th>
                    <th>Nama & Tipe Properti</th>
                    <th>Harga</th>
                    <th>Spesifikasi</th>
                    <th>Status Kavling</th>
                    <th class="text-end pe-4" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($properties)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-houses fs-1 text-secondary d-block mb-2 opacity-50"></i>
                            <div class="fw-semibold">Belum ada data properti</div>
                            <small>Klik tombol "Tambah Properti" di atas untuk mendaftarkan unit baru.</small>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($properties as $prop): ?>
                        <tr>
                            <td class="ps-4">
                                <?php 
                                $img = $prop['primary_image'] ?? '';
                                if (empty($img)) {
                                    $img = 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=100&auto=format&fit=crop&q=80';
                                } elseif (!str_starts_with($img, 'http')) {
                                    $img = base_url('uploads/properties/' . $img);
                                }
                                ?>
                                <img src="<?= esc($img) ?>" alt="Thumb" class="rounded border" style="width: 52px; height: 52px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-semibold text-dark fs-6"><?= esc($prop['title']) ?></span>
                                    <?php if (!empty($prop['is_promo'])): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                            Promo
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-0.5 text-muted small">
                                    <span>Cluster: <?= esc($prop['cluster_name'] ?? 'Cluster Utama') ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">
                                    Rp <?= number_format($prop['harga'], 0, ',', '.') ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small">
                                    LT <?= esc($prop['luas_tanah']) ?> m² • LB <?= esc($prop['luas_bangunan']) ?> m²
                                </div>
                                <div class="mt-0.5 text-muted small" style="font-size: 0.8rem;">
                                    <?= esc($prop['spesifikasi_kamar'] ?: '-') ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $status = $prop['status'] ?? 'Tersedia';
                                $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                if ($status === 'Booking') $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                elseif ($status === 'Terjual') $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                ?>
                                <span class="badge <?= $badgeClass ?> px-2.5 py-1 fw-semibold">
                                    <?= esc($status) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('properti/' . esc($prop['slug'])) ?>" target="_blank" class="btn btn-outline-secondary px-2 py-1" title="Lihat Halaman Publik">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= site_url('admin/properties/edit/' . $prop['id']) ?>" class="btn btn-outline-secondary px-2 py-1" title="Edit Properti">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger px-2 py-1" title="Hapus" onclick="confirmDelete(<?= $prop['id'] ?>, '<?= esc($prop['title']) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center small text-muted">
        <div>Menampilkan <?= count($properties) ?> unit properti</div>
        <div>Sistem Manajemen Properti Terpadu</div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-circle text-danger display-4 mb-3 d-block"></i>
                <h5 class="fw-bold mb-2">Hapus Properti?</h5>
                <p class="text-muted small mb-4" id="deleteTargetText">Data yang dihapus tidak dapat dikembalikan.</p>
                
                <form id="deleteForm" method="POST">
                    <?= csrf_field() ?>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-danger px-3">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id, title) {
    document.getElementById('deleteTargetText').textContent = 'Anda yakin ingin menghapus properti "' + title + '"?';
    document.getElementById('deleteForm').action = '<?= site_url('admin/properties/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?= $this->endSection() ?>
