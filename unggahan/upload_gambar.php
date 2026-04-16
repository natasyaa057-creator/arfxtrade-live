<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/keamanan.php';
require_once __DIR__ . '/../includes/validasi_file.php';

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

// Cek CSRF token
$token_csrf = $_POST['token_csrf'] ?? '';
if (!verifikasi_csrf($token_csrf)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token CSRF tidak valid']);
    exit;
}

// Cek file upload
if (!isset($_FILES['gambar'])) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
    exit;
}

// Upload gambar dengan validasi aman
$upload_dir = __DIR__ . '/';
$result = ValidasiFile::uploadGambar($_FILES['gambar'], $upload_dir);

if ($result['success']) {
    // Resize gambar jika terlalu besar
    $max_width = 1200;
    $max_height = 800;
    
    if ($result['width'] > $max_width || $result['height'] > $max_height) {
        $ratio = min($max_width / $result['width'], $max_height / $result['height']);
        $new_width = (int)($result['width'] * $ratio);
        $new_height = (int)($result['height'] * $ratio);
        
        $mime_type = mime_content_type($result['path']);
        $source = null;
        
        switch ($mime_type) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($result['path']);
                break;
            case 'image/png':
                $source = imagecreatefrompng($result['path']);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($result['path']);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($result['path']);
                break;
        }
        
        if ($source !== null) {
            $resized = imagecreatetruecolor($new_width, $new_height);
            
            // Preserve transparency untuk PNG dan GIF
            if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefill($resized, 0, 0, $transparent);
            }
            
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $new_width, $new_height, $result['width'], $result['height']);
            
            // Simpan gambar yang sudah diresize
            switch ($mime_type) {
                case 'image/jpeg':
                    imagejpeg($resized, $result['path'], 85);
                    break;
                case 'image/png':
                    imagepng($resized, $result['path'], 8);
                    break;
                case 'image/gif':
                    imagegif($resized, $result['path']);
                    break;
                case 'image/webp':
                    imagewebp($resized, $result['path'], 85);
                    break;
            }
            
            imagedestroy($source);
            imagedestroy($resized);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Gambar berhasil diupload',
        'filename' => $result['filename'],
        'url' => $result['url']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => $result['message']]);
}
?>








