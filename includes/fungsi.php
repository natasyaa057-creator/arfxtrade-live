<?php
declare(strict_types=1);

// Fungsi utilitas umum - Bahasa Indonesia

function basis_url(string $path = ''): string {
	$protokol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
	$root = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
	$base = rtrim($protokol . '://' . $host . ($root === '/' ? '' : $root), '/');
	$path = ltrim($path, '/');
	return $path ? $base . '/' . $path : $base . '/';
}

function buat_slug(string $teks): string {
	$teks = iconv('UTF-8', 'ASCII//TRANSLIT', $teks);
	$teks = preg_replace('~[^\pL\d]+~u', '-', $teks);
	$teks = trim($teks, '-');
	$teks = strtolower($teks);
	$teks = preg_replace('~[^-a-z0-9]+~', '', $teks);
	return $teks ?: 'slug';
}

function judul_halaman(string $judul_khusus = null): string {
	$nama_merek = 'ARFXTRADE';
	return $judul_khusus ? ($judul_khusus . ' | ' . $nama_merek) : $nama_merek;
}

function meta_deskripsi(string $deskripsi = null): string {
	$default = 'Platform personal branding trader profesional ARFXTRADE: portofolio, analisis pasar, edukasi trader, dan kolaborasi.';
	return $deskripsi ?: $default;
}

function warna_tema_utama(): string {
	return '#0a0a0a'; // hitam pekat
}

function warna_tema_aksen(): string {
	return '#d4af37'; // emas elegan
}

function catat_pengunjung(): void {
	// Mencatat kunjungan ke tabel pengunjung jika tabel tersedia
	if (!isset($GLOBALS['koneksi'])) {
		return;
	}
	$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
	$tanggal = date('Y-m-d H:i:s');
	$sql = 'INSERT INTO pengunjung (ip_address, tanggal_kunjungan) VALUES (?, ?)';
	try {
		$stmt = jalankan_query_siap($sql, 'ss', [$ip, $tanggal]);
		$stmt->close();
	} catch (Throwable $e) {
		// Abaikan jika tabel belum dibuat
	}
}

?>




