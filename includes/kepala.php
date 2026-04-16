<?php
declare(strict_types=1);
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';
require_once __DIR__ . '/keamanan.php';
require_once __DIR__ . '/premium_guard.php';

$judul_halaman = $judul_halaman ?? judul_halaman();
$deskripsi_meta = $deskripsi_meta ?? meta_deskripsi();
catat_pengunjung();
?>
<!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= aman_html($judul_halaman) ?></title>
	<meta name="description" content="<?= aman_html($deskripsi_meta) ?>">
	<meta name="theme-color" content="<?= warna_tema_utama() ?>">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?= aman_html(basis_url('aset/css/gaya.css')) ?>">

	<style>
		html.tema-gelap body { background: <?= warna_tema_utama() ?>; color: #f2f2f2; }
		.navbar-brand { letter-spacing: .5px; }
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
		<div class="container">
			<a class="navbar-brand fw-bold teks-emas" href="<?= aman_html(basis_url('')) ?>">ARFXTRADE</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navigasiUtama" aria-controls="navigasiUtama" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navigasiUtama">
                <?php
                $sudah_login = isset($_SESSION['member_id']);
                $is_premium_nav = $sudah_login && isPremiumMember();
                ?>
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link" href="<?= aman_html(basis_url('')) ?>">Beranda</a></li>
					<li class="nav-item"><a class="nav-link" href="<?= aman_html(basis_url('tentang.php')) ?>">Tentang Saya</a></li>
					<li class="nav-item"><a class="nav-link" href="<?= aman_html(basis_url('portofolio.php')) ?>">Portofolio</a></li>
					<li class="nav-item"><a class="nav-link" href="<?= aman_html(basis_url('edukasi.php')) ?>">Edukasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= aman_html(basis_url('testimoni.php')) ?>">Testimoni & Kolaborasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= aman_html(basis_url('kontak.php')) ?>">Kontak</a></li>
                    <?php if ($is_premium_nav): ?>
                        <li class="nav-item"><a class="nav-link teks-emas" href="<?= aman_html(basis_url('member_dashboard.php')) ?>"><i class="fa-solid fa-crown me-1"></i>Dashboard Premium</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= aman_html(basis_url('login.php')) ?>"><i class="fa-solid fa-sign-in-alt me-1"></i>Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= aman_html(basis_url('member_register.php')) ?>"><i class="fa-solid fa-crown me-1"></i>Daftar Premium</a></li>
                    <?php endif; ?>
                    <?php
                    // Menu Admin disembunyikan dari navigation publik untuk keamanan
                    // Admin dapat mengakses langsung via /login.php
                    // Jika perlu menampilkan menu admin untuk admin yang sudah login, uncomment kode di bawah:
                    // if (isset($_SESSION['admin_id'])) {
                    //     echo '<li class="nav-item"><a class="nav-link" href="' . aman_html(basis_url('dashboard.php')) . '"><i class="fa-solid fa-user-shield"></i> Admin Panel</a></li>';
                    // }
                    ?>
				</ul>
				<div class="ms-lg-3 form-check form-switch">
					<input class="form-check-input" type="checkbox" role="switch" id="toggleTema">
				</div>
			</div>
		</div>
	</nav>

	<main class="pt-5"></main>




