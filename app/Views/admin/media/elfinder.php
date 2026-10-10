<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<!-- jQuery UI CSS (Local Vendor with CDN Fallback) -->
<?php if (file_exists(FCPATH . 'assets/vendor/jquery-ui/jquery-ui.min.css')): ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/jquery-ui/jquery-ui.min.css') ?>" />
<?php else: ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
<?php endif; ?>
<!-- elFinder CSS -->
<link rel="stylesheet" href="<?= base_url('assets/elfinder/css/elfinder.min.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/elfinder/css/theme.css') ?>" />
<style>
  .elfinder {
    font-family: inherit;
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
    overflow: hidden;
  }
  .elfinder-toolbar {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 6px 8px;
  }
  .elfinder-statusbar {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
  }
  [data-bs-theme="dark"] .elfinder {
    background-color: #212529;
    color: #dee2e6;
    border-color: #373b3e;
  }
  [data-bs-theme="dark"] .elfinder-toolbar,
  [data-bs-theme="dark"] .elfinder-statusbar {
    background: #2b3035;
    border-color: #373b3e;
    color: #dee2e6;
  }
  [data-bs-theme="dark"] .elfinder-workzone {
    background-color: #1e2124;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!--begin::App Content Header-->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-folder2-open text-primary me-2"></i>File Manager
        </h3>
        <p class="text-muted small mb-0">Kelola berkas, upload massal, drag-and-drop, resize gambar, dan manajemen folder.</p>
      </div>
      <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <a href="<?= site_url('admin/media/classic') ?>" class="btn btn-outline-secondary btn-sm me-2">
          <i class="bi bi-images me-1"></i> Galeri Klasik
        </a>
        <a href="<?= base_url() ?>" target="_blank" class="btn btn-primary btn-sm">
          <i class="bi bi-box-arrow-up-right me-1"></i> Buka Web
        </a>
      </div>
    </div>
  </div>
</div>
<!--end::App Content Header-->

<!--begin::App Content-->
<div class="app-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-body-tertiary d-flex align-items-center justify-content-between py-2">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-success">Aktif</span>
              <span class="text-muted small">Mendukung upload folder, kompresi otomatis, webP, zip/unzip, crop & rotate</span>
            </div>
            <div>
              <button class="btn btn-sm btn-light border" onclick="$('#elfinder').elfinder('instance').exec('reload');" title="Refresh">
                <i class="bi bi-arrow-clockwise"></i> Refresh
              </button>
            </div>
          </div>
          <div class="card-body p-2">
            <!-- elFinder Container -->
            <div id="elfinder"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--end::App Content-->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- jQuery UI JS (Local Vendor with CDN Fallback) -->
<?php if (file_exists(FCPATH . 'assets/vendor/jquery-ui/jquery-ui.min.js')): ?>
<script src="<?= base_url('assets/vendor/jquery-ui/jquery-ui.min.js') ?>"></script>
<?php else: ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<?php endif; ?>
<!-- elFinder JS -->
<script src="<?= base_url('assets/elfinder/js/elfinder.min.js') ?>"></script>
<!-- elFinder Indonesian Lang -->
<script src="<?= base_url('assets/elfinder/js/i18n/elfinder.id.js') ?>"></script>

<script>
  $(document).ready(function() {
    $('#elfinder').elfinder({
      url: '<?= site_url('admin/media/connector') ?>',
      lang: 'id',
      height: 680,
      soundPath: '<?= base_url('assets/elfinder/sounds') ?>/',
      cssAutoLoad: false,
      baseUrl: '<?= base_url('assets/elfinder') ?>/',
      commandsOptions: {
        getfile: {
          onlyURL: false,
          multiple: false,
          folders: false,
          oncomplete: 'destroy'
        }
      },
      uiOptions: {
        toolbar: [
          ['back', 'forward', 'up'],
          ['mkdir', 'mkfile', 'upload'],
          ['open', 'download', 'getfile'],
          ['info', 'quicklook'],
          ['copy', 'cut', 'paste'],
          ['rm'],
          ['duplicate', 'rename', 'edit', 'resize'],
          ['extract', 'archive'],
          ['search'],
          ['view', 'sort']
        ]
      }
    });
  });
</script>
<?= $this->endSection() ?>
