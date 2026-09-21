<?php
$companyName = $settings['company_name'] ?? 'Grand Harmoni';
$primary = $settings['primary_color'] ?? '#1e3a8a';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - <?= esc($companyName) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .form-control {
            border-color: #cbd5e1;
            padding: 0.65rem 0.85rem;
            border-radius: 0.5rem;
        }
        .form-control:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
        .btn-primary {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            border-radius: 0.5rem;
            padding: 0.65rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="p-3">

    <div class="login-card p-4 p-sm-5">
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 56px; height: 56px;">
                <i class="bi bi-buildings-fill fs-3"></i>
            </div>
            <h4 class="fw-extrabold text-dark mb-1">Admin Portal</h4>
            <p class="text-muted small"><?= esc($companyName) ?> Management System</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('admin/login') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Username atau Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="superadmin" value="<?= old('username', 'superadmin') ?>" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" value="admin123" required>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary py-2 rounded-pill fw-bold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Dashboard
                </button>
            </div>

            <div class="text-center">
                <a href="<?= site_url('/') ?>" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
