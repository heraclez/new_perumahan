<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-pencil-square text-warning me-2"></i>Editor Halaman Publik (CMS Beranda)
                    </h5>
                    <small class="text-muted">Kelola konten dan tampilan publik: edit teks, media, tombol aksi, tata letak, serta geser (drag &amp; drop) urutan tampilan setiap section.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= site_url('/') ?>" target="_blank" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark shadow-xs">
                        <i class="bi bi-window-fullscreen me-1"></i> Buka Editor di Halaman Publik
                    </a>
                    <a href="<?= site_url('admin/settings') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-palette me-1"></i> Ganti Tema Presets
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="alert alert-info border-0 rounded-0 mb-0 py-2.5 px-4 d-flex align-items-center gap-2 small">
                    <i class="bi bi-info-circle-fill fs-6 flex-shrink-0"></i>
                    <div>
                        <strong>Petunjuk:</strong> Anda dapat menggeser (drag &amp; drop) baris di bawah ini untuk mengubah urutan section secara langsung di halaman depan website, atau klik tombol <strong>[Edit]</strong> untuk mengubah teks, gambar, video, dan layout variant.
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="sectionsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center"><i class="bi bi-arrows-expand text-muted"></i></th>
                                <th style="width: 70px;" class="text-center">Urutan</th>
                                <th>Nama Section &amp; Konten</th>
                                <th style="width: 140px;">Layout Variant</th>
                                <th style="width: 130px;" class="text-center">Status</th>
                                <th style="width: 160px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sortableSections">
                            <?php foreach ($sections as $index => $sec): ?>
                                <tr data-id="<?= $sec['id'] ?>" class="section-row">
                                    <td class="text-center drag-handle" style="cursor: grab;">
                                        <i class="bi bi-grip-vertical text-muted fs-5"></i>
                                    </td>
                                    <td class="text-center fw-bold text-muted section-order-badge">
                                        #<?= $sec['sort_order'] ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 p-2 bg-light border d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                <?php
                                                $icon = 'bi-window';
                                                switch ($sec['section_key']) {
                                                    case 'hero': $icon = 'bi-star-fill text-warning'; break;
                                                    case 'properties': $icon = 'bi-houses-fill text-primary'; break;
                                                    case 'about': $icon = 'bi-info-circle-fill text-info'; break;
                                                    case 'features': $icon = 'bi-check-circle-fill text-success'; break;
                                                    case 'gallery': $icon = 'bi-images text-danger'; break;
                                                    case 'siteplan': $icon = 'bi-map-fill text-success'; break;
                                                    case 'promo': $icon = 'bi-fire text-danger'; break;
                                                    case 'cta': $icon = 'bi-megaphone-fill text-primary'; break;
                                                    case 'contact': $icon = 'bi-telephone-fill text-secondary'; break;
                                                }
                                                ?>
                                                <i class="bi <?= $icon ?> fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                    <?= esc($sec['title']) ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill" style="font-size: 0.68rem; text-transform: uppercase;">
                                                        <?= esc($sec['section_key']) ?>
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block text-truncate" style="max-width: 450px;">
                                                    <?= esc($sec['subtitle'] ?: 'Section ' . $sec['section_key']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill font-monospace" style="font-size: 0.76rem;">
                                            <i class="bi bi-grid-fill text-primary me-1"></i><?= esc($sec['layout_variant']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-toggle-status rounded-pill px-3 py-1 fw-bold <?= $sec['is_active'] ? 'btn-success' : 'btn-outline-secondary' ?>" data-id="<?= $sec['id'] ?>" title="Klik untuk mengubah status aktif">
                                            <i class="bi <?= $sec['is_active'] ? 'bi-check-circle-fill' : 'bi-x-circle' ?> me-1"></i>
                                            <span class="status-label"><?= $sec['is_active'] ? 'ACTIVE' : 'INACTIVE' ?></span>
                                        </button>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= site_url('admin/sections/edit/' . $sec['section_key']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs">
                                            <i class="bi bi-pencil-square me-1"></i> Edit Konten
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted" id="reorderStatusText">
                    <i class="bi bi-shield-check text-success me-1"></i> Status urutan tersimpan otomatis.
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh Tabel
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    const el = document.getElementById('sortableSections');
    if (el) {
        new Sortable(el, {
            handle: '.drag-handle',
            animation: 200,
            ghostClass: 'bg-primary-subtle',
            onEnd: function() {
                const order = [];
                $('#sortableSections .section-row').each(function(index) {
                    order.push($(this).data('id'));
                    $(this).find('.section-order-badge').text('#' + (index + 1));
                });

                $('#reorderStatusText').html('<span class="text-primary"><i class="bi bi-arrow-repeat spin me-1"></i> Menyimpan urutan baru...</span>');

                $.ajax({
                    url: "<?= site_url('admin/sections/reorder') ?>",
                    type: "POST",
                    data: {
                        order: order,
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.success) {
                            $('#reorderStatusText').html('<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Urutan section berhasil disimpan!</span>');
                            setTimeout(function() {
                                $('#reorderStatusText').html('<i class="bi bi-shield-check text-success me-1"></i> Status urutan tersimpan otomatis.');
                            }, 3000);
                        } else {
                            $('#reorderStatusText').html('<span class="text-danger"><i class="bi bi-x-circle me-1"></i> Gagal menyimpan urutan.</span>');
                        }
                    },
                    error: function() {
                        $('#reorderStatusText').html('<span class="text-danger"><i class="bi bi-x-circle me-1"></i> Terjadi kesalahan koneksi.</span>');
                    }
                });
            }
        });
    }

    // Toggle Active/Inactive
    $('.btn-toggle-status').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        btn.prop('disabled', true);

        $.ajax({
            url: "<?= site_url('admin/sections/toggle') ?>/" + id,
            type: "POST",
            data: {
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
            },
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false);
                if (res.success) {
                    if (res.is_active) {
                        btn.removeClass('btn-outline-secondary').addClass('btn-success');
                        btn.find('i').removeClass('bi-x-circle').addClass('bi-check-circle-fill');
                        btn.find('.status-label').text('ACTIVE');
                    } else {
                        btn.removeClass('btn-success').addClass('btn-outline-secondary');
                        btn.find('i').removeClass('bi-check-circle-fill').addClass('bi-x-circle');
                        btn.find('.status-label').text('INACTIVE');
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false);
                alert('Gagal mengubah status.');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
