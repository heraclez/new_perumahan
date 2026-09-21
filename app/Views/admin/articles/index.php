<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card border-0 shadow-sm overflow-hidden mb-4">
    <!-- Card Header with Action Button & Filter Form -->
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">Daftar Artikel &amp; Berita</h5>
                    <span class="badge bg-light text-secondary border px-2 py-1"><?= count($articles) ?> Artikel</span>
                </div>
                <small class="text-muted">Kelola artikel edukasi, tips KPR, dan update progres kawasan.</small>
            </div>

            <div class="col-md-8 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-end gap-2">
                <!-- Search & Status Filter Form -->
                <form method="GET" action="<?= site_url('admin/articles') ?>" class="d-flex align-items-center gap-2 flex-grow-1 flex-sm-grow-0">
                    <div class="input-group input-group-sm" style="max-width: 220px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0" placeholder="Cari judul artikel..." value="<?= esc($filters['q'] ?? '') ?>">
                    </div>

                    <?php if (!empty($categories)): ?>
                    <select name="category" class="form-select form-select-sm" style="max-width: 160px;" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= esc($cat) ?>" <?= ($filters['category'] ?? '') === $cat ? 'selected' : '' ?>><?= esc($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>

                    <select name="status" class="form-select form-select-sm" style="max-width: 140px;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>

                    <?php if (!empty($filters['status']) || !empty($filters['category']) || !empty($filters['q'])): ?>
                        <a href="<?= site_url('admin/articles') ?>" class="btn btn-sm btn-light text-secondary border" title="Reset Filter">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    <?php endif; ?>
                </form>

                <!-- Tambah Artikel Button -->
                <a href="<?= site_url('admin/articles/create') ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil-square me-1"></i> Tulis Artikel Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Table Body -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;" class="ps-4">Cover</th>
                    <th>Judul &amp; Ringkasan</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Status</th>
                    <th>Dibaca</th>
                    <th>Tanggal Terbit</th>
                    <th class="text-end pe-4" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($articles)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 text-secondary d-block mb-2 opacity-50"></i>
                            <div class="fw-semibold">Belum ada artikel yang ditambahkan</div>
                            <small>Mulai tulis artikel edukasi properti untuk meningkatkan trafik pencarian Google.</small>
                            <div class="mt-3">
                                <a href="<?= site_url('admin/articles/create') ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Tulis Artikel Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($articles as $art): ?>
                        <tr>
                            <td class="ps-4">
                                <?php if (!empty($art['featured_image'])): ?>
                                    <img src="<?= esc($art['featured_image']) ?>" alt="Cover" class="rounded object-fit-cover shadow-xs" style="width: 64px; height: 48px;">
                                <?php else: ?>
                                    <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center border" style="width: 64px; height: 48px;">
                                        <i class="bi bi-image text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/articles/edit/' . $art['id']) ?>" class="fw-bold text-dark text-decoration-none d-block">
                                    <?= esc($art['title']) ?>
                                </a>
                                <small class="text-muted text-truncate d-block" style="max-width: 380px;">
                                    <?= esc($art['excerpt']) ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border"><?= esc($art['category']) ?></span>
                            </td>
                            <td>
                                <small class="text-muted"><i class="bi bi-person me-1"></i><?= esc($art['author_name']) ?></small>
                            </td>
                            <td>
                                <?php if ($art['status'] === 'published'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Published
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 text-dark">
                                        <i class="bi bi-pause-circle-fill me-1"></i> Draft
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    <i class="bi bi-eye me-1"></i><?= number_format($art['views_count'] ?? 0) ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted"><?= date('d M Y', strtotime($art['created_at'])) ?></small>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <?php if ($art['status'] === 'published'): ?>
                                    <a href="<?= site_url('blog/' . $art['slug']) ?>" target="_blank" class="btn btn-outline-secondary" title="Lihat Artikel di Web">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= site_url('admin/articles/edit/' . $art['id']) ?>" class="btn btn-outline-primary" title="Edit Artikel">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= site_url('admin/articles/delete/' . $art['id']) ?>" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Artikel">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="card-footer bg-white py-3">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
