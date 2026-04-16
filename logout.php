<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/fungsi.php';
require_once __DIR__ . '/includes/keamanan.php';

// Hapus semua data session
session_destroy();

// Redirect ke halaman login
header('Location: ' . basis_url('login.php'));
exit;
?>


