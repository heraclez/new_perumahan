<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Tambah Akun Admin Baru</h5>
                    <div class="small mt-1" style="color: #64748b;">Tentukan data kredensial dan hak akses admin</div>
                </div>
                <a href="<?= site_url('admin/admins') ?>" class="btn btn-sm btn-outline-secondary px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('admin/admins/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Sarah Angelina" value="<?= old('name') ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Resmi <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="sarah@grandharmoni.com" value="<?= old('email') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username Login <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="sarah_marketing" value="<?= old('username') ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Kata Sandi (Password) <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tingkat Hak Akses (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="Admin" <?= old('role') === 'Admin' ? 'selected' : '' ?>>Admin (Sales Marketing - Katalog & Leads)</option>
                                <option value="Superadmin" <?= old('role') === 'Superadmin' ? 'selected' : '' ?>>Superadmin (Akses Pengaturan & Pengguna)</option>
                                <?php if (!empty($isDeveloper)): ?>
                                    <option value="Developer" <?= old('role') === 'Developer' ? 'selected' : '' ?>>Developer (Akses Penuh + Google & SEO Tools)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?= site_url('admin/admins') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-person-check-fill me-1"></i> Simpan Akun Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
