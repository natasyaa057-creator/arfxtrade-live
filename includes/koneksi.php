<?php
// Koneksi database MySQL (mysqli) - Bahasa Indonesia
// Gunakan kredensial sesuai instalasi lokal XAMPP Anda

declare(strict_types=1);

// Load logger untuk error logging
require_once __DIR__ . '/logger.php';

// Konfigurasi database (mendukung ENV untuk deployment seperti Render)
$host_db = getenv('DB_HOST') ?: '127.0.0.1'; // Fallback lokal XAMPP
$port_db = (int)(getenv('DB_PORT') ?: 3306);
$nama_pengguna_db = getenv('DB_USER') ?: 'root';
$kata_sandi_db = getenv('DB_PASS') ?: '';
$nama_database = getenv('DB_NAME') ?: 'db-arfxtrade';

// Mode development/production
if (!defined('ENVIRONMENT')) {
	define('ENVIRONMENT', getenv('APP_ENV') ?: 'development');
}

// Membuat koneksi dengan fallback otomatis
$koneksi = mysqli_init();
$koneksi->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);

$host_kandidat = [$host_db, 'localhost']; // coba IPv4 dulu, lalu fallback localhost biasa
$terhubung = false;
$error_koneksi = '';

foreach ($host_kandidat as $host) {
	if (@$koneksi->real_connect($host, $nama_pengguna_db, $kata_sandi_db, $nama_database, $port_db)) {
		$terhubung = true;
		break;
	} else {
		$error_koneksi = $koneksi->connect_error;
	}
}

if (!$terhubung || $koneksi->connect_errno) {
	// Log error
	Logger::dbError('Database connection failed', '', [
		'host' => $host_db,
		'database' => $nama_database,
		'error' => $error_koneksi
	]);
	
	// Tampilkan error sesuai environment
	if (ENVIRONMENT === 'development') {
		die('Koneksi database gagal: ' . htmlspecialchars($error_koneksi) . '. Pastikan layanan MySQL sedang berjalan.');
	} else {
		// Production: tampilkan pesan generik
		http_response_code(503);
		die('Layanan sedang tidak tersedia. Silakan coba lagi nanti.');
	}
}

// Set karakter koneksi ke UTF-8 untuk dukungan multibahasa
if (!$koneksi->set_charset('utf8mb4')) {
	Logger::warning('Failed to set charset to utf8mb4', ['error' => $koneksi->error]);
}

// Fungsi aman untuk melakukan prepared statement cepat
function jalankan_query_siap(string $sql, string $tipe, array $nilai) {
	global $koneksi;
	
	$stmt = $koneksi->prepare($sql);
	if (!$stmt) {
		$error = $koneksi->error;
		Logger::dbError('Failed to prepare statement', $sql, ['error' => $error]);
		
		if (ENVIRONMENT === 'development') {
			throw new Exception('Gagal menyiapkan query: ' . htmlspecialchars($error));
		} else {
			throw new Exception('Terjadi kesalahan sistem. Silakan hubungi administrator.');
		}
	}
	
	if ($tipe !== '' && !empty($nilai)) {
		if (!$stmt->bind_param($tipe, ...$nilai)) {
			$error = $stmt->error;
			$stmt->close();
			Logger::dbError('Failed to bind parameters', $sql, ['error' => $error, 'params' => $nilai]);
			
			if (ENVIRONMENT === 'development') {
				throw new Exception('Gagal bind parameter: ' . htmlspecialchars($error));
			} else {
				throw new Exception('Terjadi kesalahan sistem. Silakan hubungi administrator.');
			}
		}
	}
	
	if (!$stmt->execute()) {
		$error = $stmt->error;
		$errno = $stmt->errno;
		$stmt->close();
		Logger::dbError('Query execution failed', $sql, [
			'error' => $error,
			'errno' => $errno,
			'params' => $nilai
		]);
		
		// Handle specific error codes
		if ($errno === 1062) { // Duplicate entry
			throw new Exception('Data sudah ada dalam sistem.');
		} elseif ($errno === 1452) { // Foreign key constraint
			throw new Exception('Data yang direferensikan tidak ditemukan.');
		} elseif ($errno === 1451) { // Cannot delete or update parent row
			throw new Exception('Data tidak dapat dihapus karena masih digunakan.');
		}
		
		if (ENVIRONMENT === 'development') {
			throw new Exception('Query gagal: ' . htmlspecialchars($error));
		} else {
			throw new Exception('Terjadi kesalahan sistem. Silakan hubungi administrator.');
		}
	}
	
	return $stmt;
}

?>




