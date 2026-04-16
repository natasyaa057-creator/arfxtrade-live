<?php
declare(strict_types=1);

require_once __DIR__ . '/logger.php';
require_once __DIR__ . '/fungsi.php';

/**
 * Validasi dan upload file dengan keamanan tinggi
 */
class ValidasiFile {
    private const MAX_SIZE_IMAGE = 5 * 1024 * 1024; // 5MB
    private const MAX_SIZE_PDF = 10 * 1024 * 1024; // 10MB
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const ALLOWED_IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const ALLOWED_PDF_TYPES = ['application/pdf'];
    private const ALLOWED_PDF_EXT = ['pdf'];
    
    /**
     * Validasi dan upload file bukti pembayaran (image atau PDF)
     */
    public static function uploadBuktiPembayaran(array $file, string $upload_dir): array {
        // Validasi dasar
        $validasi = self::validasiDasar($file);
        if (!$validasi['success']) {
            return $validasi;
        }
        
        // Validasi ukuran
        $max_size = self::MAX_SIZE_PDF; // PDF bisa lebih besar
        if ($file['size'] > $max_size) {
            Logger::warning('File upload rejected: size too large', [
                'size' => $file['size'],
                'max' => $max_size
            ]);
            return [
                'success' => false,
                'message' => 'Ukuran file terlalu besar. Maksimal ' . ($max_size / 1024 / 1024) . 'MB'
            ];
        }
        
        // Validasi extension
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = array_merge(self::ALLOWED_IMAGE_EXT, self::ALLOWED_PDF_EXT);
        
        if (!in_array($file_ext, $allowed_ext)) {
            Logger::warning('File upload rejected: invalid extension', [
                'extension' => $file_ext,
                'filename' => $file['name']
            ]);
            return [
                'success' => false,
                'message' => 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF.'
            ];
        }
        
        // Validasi MIME type sebenarnya
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mime = array_merge(self::ALLOWED_IMAGE_TYPES, self::ALLOWED_PDF_TYPES);
        if (!in_array($mime_type, $allowed_mime)) {
            Logger::warning('File upload rejected: MIME type mismatch', [
                'detected_mime' => $mime_type,
                'reported_mime' => $file['type'],
                'filename' => $file['name']
            ]);
            return [
                'success' => false,
                'message' => 'Tipe file tidak valid. File mungkin rusak atau tidak didukung.'
            ];
        }
        
        // Validasi konten file (untuk image)
        if (in_array($mime_type, self::ALLOWED_IMAGE_TYPES)) {
            $image_info = @getimagesize($file['tmp_name']);
            if ($image_info === false) {
                Logger::warning('File upload rejected: invalid image content', [
                    'filename' => $file['name']
                ]);
                return [
                    'success' => false,
                    'message' => 'File gambar tidak valid atau rusak.'
                ];
            }
        }
        
        // Buat direktori jika belum ada
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0700, true)) {
                Logger::error('Failed to create upload directory', ['dir' => $upload_dir]);
                return [
                    'success' => false,
                    'message' => 'Gagal membuat direktori upload.'
                ];
            }
        }
        
        // Generate nama file aman
        $safe_filename = self::generateSafeFilename($file_ext);
        $file_path = $upload_dir . $safe_filename;
        
        // Sanitasi path untuk mencegah directory traversal
        $file_path = realpath(dirname($file_path)) . '/' . basename($file_path);
        $upload_dir_real = realpath($upload_dir);
        
        if (strpos($file_path, $upload_dir_real) !== 0) {
            Logger::warning('File upload rejected: path traversal attempt', [
                'filename' => $file['name'],
                'attempted_path' => $file_path
            ]);
            return [
                'success' => false,
                'message' => 'Path file tidak valid.'
            ];
        }
        
        // Upload file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            Logger::error('Failed to move uploaded file', [
                'tmp_name' => $file['tmp_name'],
                'destination' => $file_path
            ]);
            return [
                'success' => false,
                'message' => 'Gagal mengupload file. Silakan coba lagi.'
            ];
        }
        
        // Set permission file
        chmod($file_path, 0644);
        
        Logger::info('File uploaded successfully', [
            'filename' => $safe_filename,
            'size' => $file['size'],
            'type' => $mime_type
        ]);
        
        return [
            'success' => true,
            'filename' => $safe_filename,
            'path' => $file_path,
            'relative_path' => str_replace(__DIR__ . '/../', '', $file_path)
        ];
    }
    
    /**
     * Validasi dan upload gambar
     */
    public static function uploadGambar(array $file, string $upload_dir): array {
        // Validasi dasar
        $validasi = self::validasiDasar($file);
        if (!$validasi['success']) {
            return $validasi;
        }
        
        // Validasi ukuran
        if ($file['size'] > self::MAX_SIZE_IMAGE) {
            return [
                'success' => false,
                'message' => 'Ukuran file terlalu besar. Maksimal 5MB'
            ];
        }
        
        // Validasi extension
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, self::ALLOWED_IMAGE_EXT)) {
            return [
                'success' => false,
                'message' => 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.'
            ];
        }
        
        // Validasi MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, self::ALLOWED_IMAGE_TYPES)) {
            return [
                'success' => false,
                'message' => 'Tipe file tidak valid.'
            ];
        }
        
        // Validasi konten gambar
        $image_info = @getimagesize($file['tmp_name']);
        if ($image_info === false) {
            return [
                'success' => false,
                'message' => 'File gambar tidak valid atau rusak.'
            ];
        }
        
        // Buat direktori jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0700, true);
        }
        
        // Generate nama file aman
        $safe_filename = self::generateSafeFilename($file_ext);
        $file_path = $upload_dir . $safe_filename;
        
        // Upload file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            return [
                'success' => false,
                'message' => 'Gagal mengupload gambar.'
            ];
        }
        
        // Set permission
        chmod($file_path, 0644);
        
        return [
            'success' => true,
            'filename' => $safe_filename,
            'path' => $file_path,
            'url' => basis_url('unggahan/' . $safe_filename),
            'width' => $image_info[0],
            'height' => $image_info[1]
        ];
    }
    
    /**
     * Validasi dasar file upload
     */
    private static function validasiDasar(array $file): array {
        // Cek apakah file diupload
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => 'File melebihi ukuran maksimal yang diizinkan server.',
                UPLOAD_ERR_FORM_SIZE => 'File melebihi ukuran maksimal form.',
                UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian.',
                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload.',
                UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan.',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk.',
                UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension PHP.'
            ];
            
            $error_msg = $error_messages[$file['error']] ?? 'Error tidak diketahui saat upload file.';
            Logger::warning('File upload error', ['error_code' => $file['error'], 'message' => $error_msg]);
            
            return [
                'success' => false,
                'message' => $error_msg
            ];
        }
        
        // Cek apakah file kosong
        if ($file['size'] === 0) {
            return [
                'success' => false,
                'message' => 'File yang diupload kosong.'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Generate nama file yang aman
     */
    private static function generateSafeFilename(string $extension): string {
        // Gunakan timestamp + random string untuk uniqueness
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        $safe_ext = preg_replace('/[^a-z0-9]/', '', strtolower($extension));
        
        return $timestamp . '_' . $random . '.' . $safe_ext;
    }
}

