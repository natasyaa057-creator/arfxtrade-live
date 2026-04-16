<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/koneksi.php';

// Backup database otomatis
function backup_database(): string {
    global $koneksi;
    
    $nama_database = 'db-arfxtrade';
    $tanggal = date('Y-m-d_H-i-s');
    $nama_file = "backup_db_{$nama_database}_{$tanggal}.sql";
    $path_backup = __DIR__ . "/backup/{$nama_file}";
    
    // Buat direktori backup jika belum ada
    if (!is_dir(__DIR__ . '/backup')) {
        mkdir(__DIR__ . '/backup', 0755, true);
    }
    
    // Dapatkan daftar tabel
    $tables = [];
    $result = $koneksi->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    $sql = "-- Backup Database: {$nama_database}\n";
    $sql .= "-- Tanggal: " . date('Y-m-d H:i:s') . "\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tables as $table) {
        // Structure
        $sql .= "-- Struktur tabel `{$table}`\n";
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $result = $koneksi->query("SHOW CREATE TABLE `{$table}`");
        $row = $result->fetch_array();
        $sql .= $row[1] . ";\n\n";
        
        // Data
        $sql .= "-- Data tabel `{$table}`\n";
        $result = $koneksi->query("SELECT * FROM `{$table}`");
        if ($result->num_rows > 0) {
            $sql .= "INSERT INTO `{$table}` VALUES\n";
            $values = [];
            while ($row = $result->fetch_array()) {
                $row_values = array_map(function($value) use ($koneksi) {
                    return "'" . $koneksi->real_escape_string($value) . "'";
                }, $row);
                $values[] = "(" . implode(',', $row_values) . ")";
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }
    }
    
    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    // Simpan file
    file_put_contents($path_backup, $sql);
    
    return $nama_file;
}

// Jalankan backup jika dipanggil langsung
if (basename($_SERVER['PHP_SELF']) === 'backup_db.php') {
    try {
        $nama_file = backup_database();
        echo "Backup berhasil: {$nama_file}";
    } catch (Exception $e) {
        echo "Error backup: " . $e->getMessage();
    }
}
?>








