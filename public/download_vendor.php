<?php
/**
 * Script Otomatis Download Vendor CSS & JS Lokal
 * Grand Harmoni Residence (SIM Perumahan)
 */

set_time_limit(300);
ini_set('memory_limit', '256M');

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'vendor';

$assetsToDownload = [
    // Bootstrap 5.3.3
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        'dest' => 'bootstrap/css/bootstrap.min.css',
        'name' => 'Bootstrap 5.3.3 CSS'
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        'dest' => 'bootstrap/js/bootstrap.bundle.min.js',
        'name' => 'Bootstrap 5.3.3 JS Bundle'
    ],
    // jQuery 3.7.1
    [
        'url'  => 'https://code.jquery.com/jquery-3.7.1.min.js',
        'dest' => 'jquery/jquery.min.js',
        'name' => 'jQuery 3.7.1'
    ],
    // Bootstrap Icons 1.11.3
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        'dest' => 'bootstrap-icons/bootstrap-icons.min.css',
        'name' => 'Bootstrap Icons CSS'
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2',
        'dest' => 'bootstrap-icons/fonts/bootstrap-icons.woff2',
        'name' => 'Bootstrap Icons Font (WOFF2)'
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff',
        'dest' => 'bootstrap-icons/fonts/bootstrap-icons.woff',
        'name' => 'Bootstrap Icons Font (WOFF)'
    ],
    // AdminLTE 4.0.0-beta3
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css',
        'dest' => 'adminlte/css/adminlte.min.css',
        'name' => 'AdminLTE 4.0 CSS'
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js',
        'dest' => 'adminlte/js/adminlte.min.js',
        'name' => 'AdminLTE 4.0 JS'
    ],
    // Popper.js 2.11.8
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js',
        'dest' => 'popper/popper.min.js',
        'name' => 'Popper.js 2.11.8'
    ],
    // OverlayScrollbars 2.11.0
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css',
        'dest' => 'overlayscrollbars/css/overlayscrollbars.min.css',
        'name' => 'OverlayScrollbars CSS'
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js',
        'dest' => 'overlayscrollbars/js/overlayscrollbars.browser.es6.min.js',
        'name' => 'OverlayScrollbars JS'
    ],
    // Font Source Sans 3 (Admin)
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css',
        'dest' => 'source-sans-3/index.css',
        'name' => 'Source Sans 3 CSS (Admin)'
    ],
    // jQuery UI 1.13.2 (for File Manager elFinder)
    [
        'url'  => 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css',
        'dest' => 'jquery-ui/jquery-ui.min.css',
        'name' => 'jQuery UI 1.13.2 CSS'
    ],
    [
        'url'  => 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js',
        'dest' => 'jquery-ui/jquery-ui.min.js',
        'name' => 'jQuery UI 1.13.2 JS'
    ]
];

function downloadFile($url, $filepath) {
    $dir = dirname($filepath);
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
            return [false, "Gagal membuat direktori $dir"];
        }
    }

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && $content !== false && strlen($content) > 0) {
            file_put_contents($filepath, $content);
            return [true, strlen($content)];
        }
        return [false, "HTTP $httpCode " . $err];
    } else {
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
                'timeout' => 30
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];
        $ctx = stream_context_create($opts);
        $content = @file_get_contents($url, false, $ctx);
        if ($content !== false && strlen($content) > 0) {
            file_put_contents($filepath, $content);
            return [true, strlen($content)];
        }
        return [false, "Gagal download via file_get_contents"];
    }
}

$action = $_GET['action'] ?? 'preview';
$results = [];

if ($action === 'download') {
    foreach ($assetsToDownload as $item) {
        $targetPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $item['dest']);
        list($success, $msg) = downloadFile($item['url'], $targetPath);
        $results[] = [
            'name'    => $item['name'],
            'dest'    => 'public/assets/vendor/' . $item['dest'],
            'success' => $success,
            'size'    => $success ? number_format($msg / 1024, 1) . ' KB' : $msg
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloader Aset Offline SIM Perumahan</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; margin: 0; line-height: 1.5; }
        .container { max-width: 760px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid #334155; }
        h1 { margin-top: 0; font-size: 1.5rem; color: #38bdf8; display: flex; align-items: center; gap: 0.5rem; }
        p { color: #94a3b8; font-size: 0.95rem; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; background: #0284c7; color: white; border-radius: 8px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; transition: 0.2s; }
        .btn:hover { background: #0369a1; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; font-size: 0.88rem; }
        th, td { padding: 0.65rem 0.75rem; text-align: left; border-bottom: 1px solid #334155; }
        th { color: #cbd5e1; background: #0f172a; }
        .badge { display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .badge-success { background: #064e3b; color: #34d399; }
        .badge-danger { background: #7f1d1d; color: #f87171; }
        .badge-wait { background: #334155; color: #94a3b8; }
        .mt-4 { margin-top: 1.5rem; }
    </style>
</head>
<body>
<div class="container">
    <h1>📦 Pengunduh Aset Lokal (Offline Ready)</h1>
    <p>Skrip ini akan mengunduh semua berkas CSS & JS eksternal (Bootstrap 5, jQuery, Bootstrap Icons + Font WOFF2, AdminLTE 4, dll.) dan menyimpannya secara lokal ke dalam direktori <code>public/assets/vendor/</code>.</p>

    <?php if ($action !== 'download'): ?>
        <p>Aset yang akan diunduh: <strong><?= count($assetsToDownload) ?> berkas</strong>.</p>
        <div class="mt-4">
            <a href="?action=download" class="btn">🚀 Mulai Unduh Semua Aset Sekarang</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama Paket</th>
                    <th>Tujuan Lokal</th>
                    <th>Status Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assetsToDownload as $item): ?>
                    <?php 
                    $targetPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $item['dest']);
                    $exists = file_exists($targetPath);
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                        <td><code><?= htmlspecialchars($item['dest']) ?></code></td>
                        <td>
                            <?php if ($exists): ?>
                                <span class="badge badge-success">✓ Sudah Ada (<?= number_format(filesize($targetPath)/1024, 1) ?> KB)</span>
                            <?php else: ?>
                                <span class="badge badge-wait">Belum Diunduh</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h2 style="font-size: 1.15rem; color: #34d399;">✓ Proses Unduh Selesai</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Berkas</th>
                    <th>Lokasi Tersimpan</th>
                    <th>Status</th>
                    <th>Ukuran</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $res): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($res['name']) ?></strong></td>
                        <td><code><?= htmlspecialchars($res['dest']) ?></code></td>
                        <td>
                            <?php if ($res['success']): ?>
                                <span class="badge badge-success">✓ Sukses</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Gagal</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($res['size']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-4" style="display: flex; gap: 0.75rem;">
            <a href="?action=preview" class="btn">Periksa Ulang</a>
            <a href="/" class="btn btn-success">Kembali ke Beranda Website</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
