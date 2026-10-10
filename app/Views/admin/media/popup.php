<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Pilih Media') ?></title>
  
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
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background: #f4f6f9;
    }
    #elfinder {
      height: 100vh !important;
      border: none;
    }
    .elfinder {
      border-radius: 0;
    }
  </style>
</head>
<body>

  <div id="elfinder"></div>

  <!-- jQuery (Local Vendor with CDN Fallback) -->
  <?php if (file_exists(FCPATH . 'assets/vendor/jquery/jquery.min.js')): ?>
  <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
  <?php else: ?>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <?php endif; ?>

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
    const targetId = <?= json_encode($targetId ?? '') ?>;
    const previewId = <?= json_encode($previewId ?? '') ?>;

    function handleFileSelect(file, fm) {
      // file.url contains the full or relative URL
      let fileUrl = file.url;
      
      // Fallback if URL is not absolute:
      if (!fileUrl.startsWith('http') && !fileUrl.startsWith('/')) {
        fileUrl = '<?= base_url() ?>/' + fileUrl;
      }

      // 1. Check if inside iframe and parent window is accessible
      if (window.parent && window.parent !== window) {
        if (targetId && window.parent.document.getElementById(targetId)) {
          const inputEl = window.parent.document.getElementById(targetId);
          inputEl.value = fileUrl;
          inputEl.dispatchEvent(new Event('input', { bubbles: true }));
          inputEl.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (previewId && window.parent.document.getElementById(previewId)) {
          const prevEl = window.parent.document.getElementById(previewId);
          prevEl.src = fileUrl;
          prevEl.style.display = 'block';
        }

        if (typeof window.parent.selectMediaUrl === 'function') {
          window.parent.selectMediaUrl(fileUrl);
        }

        if (typeof window.parent.onMediaSelectedFromElFinder === 'function') {
          window.parent.onMediaSelectedFromElFinder(fileUrl, targetId, previewId, file);
        }

        if (typeof window.parent.closeElfinderModal === 'function') {
          window.parent.closeElfinderModal();
        }
      }

      // 2. Check if opened via window.open (popup)
      if (window.opener && !window.opener.closed) {
        if (targetId && window.opener.document.getElementById(targetId)) {
          const inputEl = window.opener.document.getElementById(targetId);
          inputEl.value = fileUrl;
          inputEl.dispatchEvent(new Event('input', { bubbles: true }));
          inputEl.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (previewId && window.opener.document.getElementById(previewId)) {
          const prevEl = window.opener.document.getElementById(previewId);
          prevEl.src = fileUrl;
          prevEl.style.display = 'block';
        }

        if (typeof window.opener.onMediaSelectedFromElFinder === 'function') {
          window.opener.onMediaSelectedFromElFinder(fileUrl, targetId, previewId, file);
        }

        window.close();
      }

      // 3. Post message for cross-context listeners
      window.parent.postMessage({
        type: 'elfinder-select',
        url: fileUrl,
        targetId: targetId,
        previewId: previewId,
        file: file
      }, '*');
    }

    $(document).ready(function() {
      $('#elfinder').elfinder({
        url: '<?= site_url('admin/media/connector') ?>',
        lang: 'id',
        height: '100%',
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
        getFileCallback: function(file, fm) {
          handleFileSelect(file, fm);
        },
        uiOptions: {
          toolbar: [
            ['back', 'forward', 'up'],
            ['mkdir', 'upload'],
            ['open', 'getfile'],
            ['quicklook', 'info'],
            ['copy', 'cut', 'paste', 'rm'],
            ['duplicate', 'rename', 'resize'],
            ['search'],
            ['view', 'sort']
          ]
        }
      }).elfinder('instance');
    });
  </script>
</body>
</html>
