<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * ImageOptimizer Service
 * 
 * Bertanggung jawab untuk:
 * 1. Otomatis kompres dan konversi gambar (JPG, PNG, GIF, BMP) ke format WebP modern.
 * 2. Menjaga orientasi EXIF kamera smartphone agar tidak terbalik/miring.
 * 3. Menjaga transparansi alpha channel untuk gambar PNG transparan.
 * 4. Resize cerdas (downscale proporsional) jika dimensi melebihi batas resolusi web (default 1920px).
 * 5. Menghasilkan teks alt ramah SEO (Google Images) secara otomatis dari nama berkas/caption.
 */
class ImageOptimizer
{
    /**
     * Konversi dan optimasi file unggahan menjadi WebP
     *
     * @param UploadedFile|string $file Objek file CI4 atau path absolut file lokal
     * @param string $destinationDir Direktori tujuan (path absolut)
     * @param string|null $customBasename Nama dasar file (opsional)
     * @param int $maxWidth Lebar maksimal gambar (pixel)
     * @param int $quality Kualitas WebP (1-100, rekomendasi 82)
     * @return array Metadata hasil konversi & optimasi
     */
    public static function convertToWebp(
        $file,
        string $destinationDir,
        ?string $customBasename = null,
        int $maxWidth = 1920,
        int $quality = 82
    ): array {
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        $sourcePath = $file instanceof UploadedFile ? $file->getTempName() : (string) $file;
        $clientName = $file instanceof UploadedFile ? $file->getClientName() : basename((string) $file);
        $originalSize = $file instanceof UploadedFile ? $file->getSize() : (file_exists($sourcePath) ? filesize($sourcePath) : 0);
        $mime = $file instanceof UploadedFile ? $file->getMimeType() : (file_exists($sourcePath) ? mime_content_type($sourcePath) : '');

        // Jika bukan gambar raster (misal SVG, PDF, MP4), simpan apa adanya
        $rasterMimes = [
            'image/jpeg', 'image/jpg', 'image/pjpeg',
            'image/png', 'image/x-png',
            'image/webp',
            'image/gif',
            'image/bmp', 'image/x-ms-bmp',
        ];

        $cleanBase = $customBasename ?: pathinfo($clientName, PATHINFO_FILENAME);
        // Sanitize base name untuk URL ramah SEO
        $cleanBase = self::slugify($cleanBase);
        $uniqueSuffix = bin2hex(random_bytes(4));

        if (!in_array($mime, $rasterMimes, true) || !extension_loaded('gd')) {
            // Fallback: simpan file asli tanpa konversi raster
            $extension = $file instanceof UploadedFile ? $file->getClientExtension() : pathinfo((string) $file, PATHINFO_EXTENSION);
            $finalFilename = "{$cleanBase}-{$uniqueSuffix}.{$extension}";
            $destPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalFilename;

            if ($file instanceof UploadedFile && $file->isValid() && !$file->hasMoved()) {
                $file->move($destinationDir, $finalFilename);
            } else {
                copy($sourcePath, $destPath);
            }

            return [
                'success'       => true,
                'filename'      => $finalFilename,
                'full_path'     => $destPath,
                'file_type'     => $mime,
                'file_size'     => file_exists($destPath) ? filesize($destPath) : $originalSize,
                'original_size' => $originalSize,
                'saved_percent' => 0,
                'is_webp'       => false,
            ];
        }

        // Baca gambar sumber ke resource GD
        $img = self::createImageResource($sourcePath, $mime);
        if (!$img) {
            // Jika pembacaan GD gagal, simpan file asli
            $extension = $file instanceof UploadedFile ? $file->getClientExtension() : 'jpg';
            $finalFilename = "{$cleanBase}-{$uniqueSuffix}.{$extension}";
            $destPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalFilename;

            if ($file instanceof UploadedFile && $file->isValid() && !$file->hasMoved()) {
                $file->move($destinationDir, $finalFilename);
            } else {
                copy($sourcePath, $destPath);
            }

            return [
                'success'       => true,
                'filename'      => $finalFilename,
                'full_path'     => $destPath,
                'file_type'     => $mime,
                'file_size'     => file_exists($destPath) ? filesize($destPath) : $originalSize,
                'original_size' => $originalSize,
                'saved_percent' => 0,
                'is_webp'       => false,
            ];
        }

        // Perbaiki rotasi EXIF foto HP (iPhone / Android)
        $img = self::correctExifOrientation($img, $sourcePath);

        $origWidth = imagesx($img);
        $origHeight = imagesy($img);

        // Proporsional resize jika melebihi batas maksimal lebar web
        $targetWidth = $origWidth;
        $targetHeight = $origHeight;

        if ($origWidth > $maxWidth) {
            $targetWidth = $maxWidth;
            $targetHeight = (int) round($origHeight * ($maxWidth / $origWidth));
        }

        // Buat canvas true color
        $targetImg = imagecreatetruecolor($targetWidth, $targetHeight);

        // Pertahankan transparansi alpha channel untuk PNG/WebP transparan
        imagealphablending($targetImg, false);
        imagesavealpha($targetImg, true);
        $transparent = imagecolorallocatealpha($targetImg, 0, 0, 0, 127);
        imagefilledrectangle($targetImg, 0, 0, $targetWidth, $targetHeight, $transparent);

        // Resample dengan kualitas tinggi (bicubic filtering)
        imagecopyresampled(
            $targetImg,
            $img,
            0, 0, 0, 0,
            $targetWidth,
            $targetHeight,
            $origWidth,
            $origHeight
        );

        // Simpan sebagai .webp
        $finalFilename = "{$cleanBase}-{$uniqueSuffix}.webp";
        $destPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalFilename;

        imagewebp($targetImg, $destPath, $quality);

        // Bebaskan memori jika sebelum PHP 8.5
        if (PHP_VERSION_ID < 80500) {
            @imagedestroy($img);
            @imagedestroy($targetImg);
        }

        $finalSize = file_exists($destPath) ? filesize($destPath) : 0;
        $savedPercent = ($originalSize > 0 && $finalSize > 0)
            ? max(0, round((1 - ($finalSize / $originalSize)) * 100, 1))
            : 0;

        return [
            'success'       => true,
            'filename'      => $finalFilename,
            'full_path'     => $destPath,
            'file_type'     => 'image/webp',
            'file_size'     => $finalSize,
            'width'         => $targetWidth,
            'height'        => $targetHeight,
            'original_size' => $originalSize,
            'saved_percent' => $savedPercent,
            'is_webp'       => true,
        ];
    }

    /**
     * Otomatis membuat teks alt yang deskriptif dan ramah SEO dari nama berkas atau caption
     */
    public static function generateAltText(?string $caption, string $filename, ?string $siteName = null): string
    {
        $caption = trim((string) $caption);
        if ($caption !== '' && !self::isRawFilename($caption)) {
            $text = $caption;
        } else {
            // Bersihkan nama file
            $base = pathinfo($filename, PATHINFO_FILENAME);
            // Hapus pola hash hex/angka acak di akhir (misal -a1b2c3d4 atau _1600585154340)
            $base = preg_replace('/[-_][a-f0-9]{6,16}$/i', '', $base);
            $base = preg_replace('/[-_]\d{10,}$/i', '', $base);
            // Hapus kata penanda camera seperti IMG, DSC, PHOTO, WA
            $base = preg_replace('/^(img|dsc|photo|wa|wp)[-_]?\d+[-_]?/i', '', $base);
            // Ganti pemisah kata dengan spasi
            $base = str_replace(['-', '_', '.'], ' ', $base);
            $base = trim(preg_replace('/\s+/', ' ', $base));

            if ($base === '' || strlen($base) < 3) {
                $base = 'Foto Properti & Kawasan Hunian';
            } else {
                $base = ucwords(strtolower($base));
            }

            $text = $base;
        }

        // Tambahkan konteks nama perumahan jika belum tercantum
        if ($siteName && !str_contains(strtolower($text), strtolower($siteName))) {
            $text .= " - {$siteName}";
        }

        return $text;
    }

    /**
     * Membaca file menjadi GD image resource berdasarkan format
     */
    private static function createImageResource(string $path, string $mime)
    {
        if (!file_exists($path)) {
            return false;
        }

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/pjpeg':
                return @imagecreatefromjpeg($path);

            case 'image/png':
            case 'image/x-png':
                return @imagecreatefrompng($path);

            case 'image/webp':
                return @imagecreatefromwebp($path);

            case 'image/gif':
                return @imagecreatefromgif($path);

            case 'image/bmp':
            case 'image/x-ms-bmp':
                return @imagecreatefrombmp($path);

            default:
                // Coba deteksi via string bytes
                $content = @file_get_contents($path);
                return $content ? @imagecreatefromstring($content) : false;
        }
    }

    /**
     * Memperbaiki orientasi foto JPEG berdasarkan tag EXIF
     */
    private static function correctExifOrientation($img, string $path)
    {
        if (!function_exists('exif_read_data')) {
            return $img;
        }

        try {
            $exif = @exif_read_data($path);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $img = imagerotate($img, 180, 0);
                        break;
                    case 6:
                        $img = imagerotate($img, -90, 0);
                        break;
                    case 8:
                        $img = imagerotate($img, 90, 0);
                        break;
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika EXIF corrupt/tidak terbaca
        }

        return $img;
    }

    /**
     * Mengubah string teks menjadi slug yang aman untuk nama file
     */
    public static function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        return $text ?: 'media';
    }

    /**
     * Memeriksa apakah string adalah nama file mentah (misal: "IMG_2026.jpg")
     */
    private static function isRawFilename(string $str): bool
    {
        return (bool) preg_match('/^(img|dsc|photo|wa|wp|file|image)[-_]?\d+/i', $str)
            || (bool) preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $str);
    }
}
