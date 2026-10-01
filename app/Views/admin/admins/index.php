<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 px-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Manajemen Akun Admin</h5>
            <div class="small mt-1" style="color: #64748b;">Kelola pengguna dengan hak akses Superadmin dan Admin Sales Marketing</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if (!empty($isDeveloper)): ?>
                <?php if (!empty($showDev)): ?>
                    <a href="<?= site_url('admin/admins') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Sembunyikan Akun Developer">
                        <i class="bi bi-eye-slash me-1"></i> Sembunyikan Akun Dev
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('admin/admins?show_dev=1') ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Tampilkan Akun Developer (Khusus Developer)">
                        <i class="bi bi-code-slash me-1"></i> Mode Dev (Tampilkan Akun Dev)
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="<?= site_url('admin/admins/create') ?>" class="btn btn-primary rounded-pill px-3.5">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Admin Baru
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Hak Akses (Role)</th>
                    <th>Terdaftar Pada</th>
                    <th class="text-end" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $currentId = (int) session()->get('admin_id');
                ?>
                <?php if (empty($admins)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                            Belum ada akun admin biasa yang terdaftar.
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($admins as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark fs-6"><?= esc($user['name']) ?></div>
                                    <?php if ($user['id'] === $currentId): ?>
                                        <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.68rem;">Akun Anda (Sedang Aktif)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><code class="fw-semibold px-2 py-1 rounded bg-light border text-secondary" style="font-size: 0.8rem;"><?= esc($user['username']) ?></code></td>
                        <td class="fw-medium text-dark"><?= esc($user['email']) ?></td>
                        <td>
                            <?php if ($user['role'] === 'Developer'): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-code-slash me-1"></i> Developer
                                </span>
                            <?php elseif ($user['role'] === 'Superadmin'): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-shield-check me-1"></i> Superadmin
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill">
                                    <i class="bi bi-person-badge me-1"></i> Admin
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="small fw-medium" style="color: #475569;"><?= date('d M Y, H:i', strtotime($user['created_at'])) ?></td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="<?= site_url('admin/admins/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-secondary px-2.5" title="Edit Akun">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <?php if ($user['id'] !== $currentId): ?>
                                    <form action="<?= site_url('admin/admins/delete/' . $user['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin <?= esc($user['name']) ?>?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2.5" title="Hapus Akun">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light border text-muted px-2.5" disabled title="Anda tidak bisa menghapus akun yang sedang aktif">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
