<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 px-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Daftar Prospek Masuk (Leads)</h5>
            <div class="small mt-1" style="color: #64748b;">Calon pembeli yang telah mengunduh brosur atau meminta konsultasi unit</div>
        </div>
        <div>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill">
                <i class="bi bi-people-fill me-1"></i> Total <?= count($leads) ?> Prospek
            </span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Nama Calon Pembeli</th>
                    <th>Nomor WhatsApp</th>
                    <th>Unit yang Diminati</th>
                    <th>Waktu Masuk</th>
                    <th class="text-end" style="width: 140px;">Aksi Follow-Up</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color: #94a3b8;">
                            <i class="bi bi-inbox fs-1 d-block mb-2" style="color: #cbd5e1;"></i>
                            Belum ada data prospek yang masuk.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $no = 1;
                    $isAdminRole = session()->get('admin_role');
                    ?>
                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td class="fw-semibold" style="color: #94a3b8;"><?= $no++ ?></td>
                            <td>
                                <div class="fw-semibold text-dark fs-6"><?= esc($lead['nama_prospek']) ?></div>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= esc($lead['no_wa']) ?>?text=Halo%20<?= urlencode($lead['nama_prospek']) ?>,%20saya%20marketing%20dari%20<?= urlencode($settings['company_name'] ?? 'Grand Harmoni') ?>.%20Terima%20kasih%20telah%20mengunduh%20brosur%20unit%20<?= urlencode($lead['property_title'] ?? 'Perumahan') ?>.%20Apakah%20ada%20informasi%20tambahan%20yang%20bisa%20kami%20bantu?" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-medium">
                                    <i class="bi bi-whatsapp me-1"></i> +<?= esc($lead['no_wa']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($lead['property_title'])): ?>
                                    <a href="<?= site_url('properti/' . $lead['property_slug']) ?>" target="_blank" class="badge bg-light text-secondary border text-decoration-none px-2.5 py-1.5">
                                        <i class="bi bi-house-door me-1 text-primary"></i> <?= esc($lead['property_title']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border px-2.5 py-1.5">Brosur Global</span>
                                <?php endif; ?>
                            </td>
                            <td class="small fw-medium" style="color: #475569;">
                                <i class="bi bi-clock me-1"></i> <?= date('d M Y, H:i', strtotime($lead['created_at'])) ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="https://wa.me/<?= esc($lead['no_wa']) ?>" target="_blank" class="btn btn-sm btn-success text-white px-2.5" title="Chat Langsung">
                                        <i class="bi bi-chat-dots-fill"></i>
                                    </a>
                                    <?php if ($isAdminRole === 'Superadmin'): ?>
                                        <form action="<?= site_url('admin/leads/delete/' . $lead['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus data prospek ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2.5" title="Hapus Data">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
