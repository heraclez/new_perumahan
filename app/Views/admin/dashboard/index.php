<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Small Box (Stat Cards) — Ref: https://adminlte.io/themes/v4/widgets/small-box.html -->
<div class="row g-3 mb-4">
    <!-- Small Box 1: Total Properti (Soft Primary) -->
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-primary">
            <div class="inner">
                <h3><?= esc($totalProperties) ?></h3>
                <p>Total Properti</p>
            </div>
            <i class="small-box-icon bi bi-houses-fill"></i>
            <a href="<?= site_url('admin/properties') ?>" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Kelola Properti <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>
    </div>

    <!-- Small Box 2: Total Leads (Soft Success) -->
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-success">
            <div class="inner">
                <h3><?= esc($totalLeads) ?></h3>
                <p>Total Leads / Prospek</p>
            </div>
            <i class="small-box-icon bi bi-person-lines-fill"></i>
            <a href="<?= site_url('admin/leads') ?>" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Data Prospek Masuk <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>
    </div>

    <!-- Small Box 3: Unit Ready Stock (Soft Warning) -->
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-warning">
            <div class="inner">
                <h3><?= esc($availableProperties) ?></h3>
                <p>Unit Ready Stock</p>
            </div>
            <i class="small-box-icon bi bi-check-circle-fill"></i>
            <a href="<?= site_url('admin/properties') ?>" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                Filter Unit Tersedia <i class="bi bi-link-45deg"></i>
            </a>
        </div>
    </div>

    <!-- Small Box 4: Pengguna Admin (Native AdminLTE text-bg-info) -->
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-info">
            <div class="inner">
                <h3><?= esc($totalAdmins) ?></h3>
                <p>Pengguna Admin</p>
            </div>
            <i class="small-box-icon bi bi-shield-lock-fill"></i>
            <a href="<?= site_url('admin/admins') ?>" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Kelola Pengguna <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>
    </div>
</div>

<!-- Soft Status Quick Breakdown Bar -->
<div class="card mb-4 border-0 shadow-xs" style="background: #ffffff;">
    <div class="card-body py-2.5 px-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary border-0 px-2.5 py-1.5 fw-semibold">
                <i class="bi bi-pie-chart-fill me-1"></i> Ringkasan Stok Unit
            </span>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2 small">
            <span class="badge bg-success-subtle text-success border-0 px-2.5 py-1.5">
                <i class="bi bi-check2 me-1"></i> <?= esc($availableProperties) ?> Unit Tersedia
            </span>
            <span class="badge bg-warning-subtle text-warning border-0 px-2.5 py-1.5">
                <i class="bi bi-clock-history me-1"></i> <?= esc($bookingProperties) ?> Unit Booking
            </span>
            <span class="badge bg-danger-subtle text-danger border-0 px-2.5 py-1.5">
                <i class="bi bi-x-circle me-1"></i> <?= esc($soldProperties) ?> Unit Terjual
            </span>
    </div>
</div>



<div class="row g-4">
    <!-- Recent Leads Card -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill text-primary"></i> 5 Prospek Terbaru Masuk
                </h3>
                <div class="card-tools">
                    <a href="<?= site_url('admin/leads') ?>" class="btn btn-tool" title="Buka Halaman Leads">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse" aria-label="Collapse card">
                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Nama Prospek</th>
                            <th>No. WhatsApp</th>
                            <th>Unit Pilihan</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentLeads)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color: #94a3b8;">Belum ada prospek masuk.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentLeads as $lead): ?>
                                <tr>
                                    <td class="fw-semibold text-dark ps-3"><?= esc($lead['nama_prospek']) ?></td>
                                    <td>
                                        <a href="https://wa.me/<?= esc($lead['no_wa']) ?>?text=Halo%20<?= urlencode($lead['nama_prospek']) ?>,%20terima%20kasih%20telah%20tertarik%20dengan%20properti%20kami." target="_blank" class="text-success text-decoration-none fw-medium">
                                            <i class="bi bi-whatsapp"></i> +<?= esc($lead['no_wa']) ?>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-secondary border"><?= esc($lead['property_title'] ?? 'Katalog Global') ?></span></td>
                                    <td class="text-end pe-3">
                                        <a href="https://wa.me/<?= esc($lead['no_wa']) ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-0.5" title="Hubungi WA">
                                            <i class="bi bi-chat-dots-fill"></i> Chat
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix py-2 px-3 text-end bg-body">
                <a href="<?= site_url('admin/leads') ?>" class="text-decoration-none small fw-semibold">
                    Lihat Seluruh Prospek <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Properties Card -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title d-flex align-items-center gap-2">
                    <i class="bi bi-houses text-primary"></i> Tipe Properti Terkini
                </h3>
                <div class="card-tools">
                    <a href="<?= site_url('admin/properties') ?>" class="btn btn-tool" title="Buka Katalog Properti">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse" aria-label="Collapse card">
                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Tipe Unit</th>
                            <th>Harga Mulai</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentProperties)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color: #94a3b8;">Belum ada properti.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentProperties as $p): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <?php 
                                             $img = $p['primary_image'] ?? '';
                                             if (empty($img)) {
                                                 $img = 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=100&auto=format&fit=crop&q=80';
                                             } elseif (!str_starts_with($img, 'http')) {
                                                 $img = base_url('uploads/properties/' . $img);
                                             }
                                            ?>
                                            <img src="<?= esc($img) ?>" class="rounded-2 border" style="width: 44px; height: 38px; object-fit: cover;" alt="Thumb">
                                            <div>
                                                <div class="fw-semibold text-dark text-truncate" style="max-width: 150px;"><?= esc($p['title']) ?></div>
                                                <div class="small" style="color: #94a3b8; font-size: 0.76rem;">LT <?= esc($p['luas_tanah']) ?> / LB <?= esc($p['luas_bangunan']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-dark">Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($p['status'] === 'Tersedia'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Tersedia</span>
                                        <?php elseif ($p['status'] === 'Booking'): ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Booking</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Terjual</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="<?= site_url('admin/properties/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary px-2.5 py-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix py-2 px-3 text-end bg-body">
                <a href="<?= site_url('admin/properties') ?>" class="text-decoration-none small fw-semibold">
                    Kelola Seluruh Properti <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

