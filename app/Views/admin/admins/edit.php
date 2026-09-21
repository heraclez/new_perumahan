<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Edit Akun Admin: <?= esc($admin['name']) ?></h5>
                    <div class="small mt-1" style="color: #64748b;">Perbarui profil pengguna atau ganti kata sandi login</div>
                </div>
                <a href="<?= site_url('admin/admins') ?>" class="btn btn-sm btn-outline-secondary px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('admin/admins/update/' . $admin['id']) ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= old('name', $admin['name']) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Resmi <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= old('email', $admin['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username Login <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="<?= old('username', $admin['username']) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                            <small class="d-block mt-1" style="color: #64748b; font-size: 0.75rem;">* Kosongkan jika tetap ingin menggunakan password lama.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tingkat Hak Akses (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="Admin" <?= old('role', $admin['role']) === 'Admin' ? 'selected' : '' ?>>Admin (Sales Marketing - Katalog & Leads)</option>
                                <option value="Superadmin" <?= old('role', $admin['role']) === 'Superadmin' ? 'selected' : '' ?>>Superadmin (Akses Pengaturan & Pengguna)</option>
                                <option value="Developer" <?= old('role', $admin['role']) === 'Developer' ? 'selected' : '' ?>>Developer (Akses Penuh + Google & SEO Tools)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?= site_url('admin/admins') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-save me-1"></i> Perbarui Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
