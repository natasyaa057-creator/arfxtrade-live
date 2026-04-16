<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/keamanan.php';

session_destroy();
header('Location: ' . basis_url('login.php?pesan=logout_sukses'));
exit;





