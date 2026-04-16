<?php
declare(strict_types=1);
require_once __DIR__ . '/keamanan.php';
require_once __DIR__ . '/fungsi.php';

// Cek apakah user sudah login sebagai admin
function cek_login_admin(): void {
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_nama'])) {
        header('Location: ' . basis_url('login.php'));
        exit;
    }
}

// Cek login admin dan redirect jika belum login
cek_login_admin();
?>


