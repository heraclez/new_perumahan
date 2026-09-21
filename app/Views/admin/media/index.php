<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-folder2-open text-primary me-2"></i>Media Manager (Galeri Aset &amp; File)
                    </h5>
                    <small class="text-muted">Kelola gambar, video, dan dokumen PDF untuk digunakan di Hero, Galeri, About, Promo, dan CTA.</small>
                </div>
                <div>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah Berkas Baru
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <?php if (empty($items)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-images fs-1 d-block mb-3 text-secondary opacity-50"></i>
                        <h5>Belum ada berkas media</h5>
                        <p class="small">Klik tombol <strong>Unggah Berkas Baru</strong> di atas untuk menambahkan foto, video, atau dokumen.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($items as $item): ?>
                            <?php 
                            $isImg = str_starts_with($item['file_type'], 'image/');
                            $isVid = str_starts_with($item['file_type'], 'video/');
                            $isPdf = $item['file_type'] === 'application/pdf';
                            $url = base_url($item['file_path']);
                            $size = number_format($item['file_size'] / 1024, 1) . ' KB';
                            ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100 border shadow-xs overflow-hidden rounded-3">
                                    <div class="position-relative bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                        <?php if ($isImg): ?>
                                            <img src="<?= esc($url) ?>" alt="<?= esc($item['caption']) ?>" class="w-100 h-100 object-fit-cover">
                                        <?php elseif ($isVid): ?>
                                            <i class="bi bi-play-btn-fill text-danger fs-1"></i>
                                            <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark opacity-75">VIDEO</span>
                                        <?php elseif ($isPdf): ?>
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-1"></i>
                                            <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark opacity-75">PDF</span>
                                        <?php else: ?>
                                            <i class="bi bi-file-earmark-fill text-secondary fs-1"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark text-truncate mb-1" title="<?= esc($item['caption'] ?: $item['original_name']) ?>">
                                            <?= esc($item['caption'] ?: $item['original_name']) ?>
                                        </h6>
                                        <div class="d-flex justify-content-between text-muted small mb-2">
                                            <span><?= $size ?></span>
                                            <span><?= date('d M Y', strtotime($item['created_at'])) ?></span>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill flex-grow-1 btn-copy-url" data-url="<?= esc($url) ?>" title="Salin URL Berkas">
                                                <i class="bi bi-clipboard me-1"></i> Salin URL
                                            </button>
                                            <form action="<?= site_url('admin/media/delete/' . $item['id']) ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus media ini?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" title="Hapus Media">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upload Media Modal -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-labelledby="uploadMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="<?= site_url('admin/media/upload') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark" id="uploadMediaModalLabel">
                        <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Unggah Berkas Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Berkas <span class="text-danger">*</span></label>
                        <input type="file" name="media_file" class="form-control" required accept="image/*,video/mp4,video/webm,application/pdf">
                        <div class="form-text">Mendukung file: JPG, PNG, WEBP, SVG, MP4, PDF (Maksimal 25MB).</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Keterangan / Caption (Opsional)</label>
                        <input type="text" name="caption" class="form-control" placeholder="Contoh: Tampak Depan Cluster Jasmine">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-upload me-1"></i> Mulai Unggah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('.btn-copy-url').on('click', function() {
        const url = $(this).data('url');
        const btn = $(this);
        navigator.clipboard.writeText(url).then(function() {
            const original = btn.html();
            btn.html('<i class="bi bi-check2 text-success me-1"></i> Tersalin!');
            setTimeout(function() {
                btn.html(original);
            }, 2000);
        });
    });
});
</script>
<?= $this->endSection() ?>
