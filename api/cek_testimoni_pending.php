<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/keamanan.php';

header('Content-Type: application/json');

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

try {
    $sql = "SELECT COUNT(*) as jumlah FROM testimoni WHERE tampil = 0";
    $stmt = jalankan_query_siap($sql, '', []);
    $hasil = $stmt->get_result();
    $data = $hasil->fetch_assoc();
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'jumlah' => (int)$data['jumlah']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>








