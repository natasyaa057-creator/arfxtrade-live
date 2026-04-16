<?php
declare(strict_types=1);

// Proteksi CSRF & sanitasi input sederhana - Bahasa Indonesia

if (session_status() === PHP_SESSION_NONE) {
	// Konfigurasi session security
	ini_set('session.cookie_httponly', '1');
	ini_set('session.use_only_cookies', '1');
	ini_set('session.cookie_samesite', 'Strict');
	
	// Untuk production dengan HTTPS, uncomment baris berikut:
	// ini_set('session.cookie_secure', '1');
	
	// Session timeout (30 menit)
	ini_set('session.gc_maxlifetime', '1800');
	
	// Regenerate session ID setiap 5 request untuk mencegah session fixation
	if (!isset($_SESSION['created'])) {
		$_SESSION['created'] = time();
	} elseif (time() - $_SESSION['created'] > 300) {
		// Regenerate session ID setiap 5 menit
		session_regenerate_id(true);
		$_SESSION['created'] = time();
	}
	
	session_start();
	
	// Cek session timeout
	if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
		// Session expired (30 menit)
		session_unset();
		session_destroy();
		session_start();
	}
	
	$_SESSION['last_activity'] = time();
}

function token_csrf(): string {
	if (empty($_SESSION['token_csrf'])) {
		$_SESSION['token_csrf'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['token_csrf'];
}

function verifikasi_csrf(?string $token): bool {
	return isset($_SESSION['token_csrf']) && hash_equals($_SESSION['token_csrf'], (string)$token);
}

function aman_html(string $teks): string {
	return htmlspecialchars($teks, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

?>




