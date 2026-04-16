<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/premium_guard.php';

$judul_halaman = 'Beranda - ARFXTRADE';
$deskripsi_meta = 'Trader profesional ARFXTRADE – portofolio, analisis pasar, edukasi, dan kolaborasi. Akses premium untuk analisa harian, signal trading, dan tools eksklusif.';
require_once __DIR__ . '/includes/kepala.php';

$member_info = getMemberInfo();
$is_logged_in = isset($_SESSION['member_id']);
$is_premium = isPremiumMember();
$pesan_error = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_error']);

$preview_modules = [
    [
        'ikon' => 'fa-chart-line',
        'judul' => 'Analisa Harian',
        'deskripsi' => 'Outlook pasar forex berbasis data makro dan price action.',
        'slug' => 'analisis_harian.php'
    ],
    [
        'ikon' => 'fa-signal',
        'judul' => 'Signal Trading',
        'deskripsi' => 'Setup high-conviction lengkap dengan level entry/exit.',
        'slug' => 'signal_trading.php'
    ],
    [
        'ikon' => 'fa-chart-area',
        'judul' => 'Chart & Indikator',
        'deskripsi' => 'Template indikator dan charting khusus komunitas.',
        'slug' => 'chart_indikator.php'
    ],
    [
        'ikon' => 'fa-download',
        'judul' => 'Download Premium',
        'deskripsi' => 'Workbook, checklist, dan tools otomasi risiko.',
        'slug' => 'download_premium.php'
    ]
];

$benefits = [
    [
        'judul' => 'Transparansi Porto',
        'deskripsi' => 'Pergerakan portofolio didokumentasikan harian lengkap dengan review keputusan.'
    ],
    [
        'judul' => 'Community Rhythm',
        'deskripsi' => 'Diskusi live mingguan, Q&A, serta bank setup siap pakai.'
    ],
    [
        'judul' => 'Framework Mindset 100x',
        'deskripsi' => 'Bukan hanya sinyal, tapi kerangka berpikir untuk menjaga konsistensi.'
    ]
];

$langkah_upgrade = [
    ['step' => '01', 'judul' => 'Registrasi', 'deskripsi' => 'Buat akun member dan lengkapi data dasar.'],
    ['step' => '02', 'judul' => 'Verifikasi', 'deskripsi' => 'Tim mengecek pembayaran & status dalam 1x24 jam.'],
    ['step' => '03', 'judul' => 'Aktifkan', 'deskripsi' => 'Dapatkan akses penuh ke dashboard premium & komunitas.']
];

// CTA Link - Hanya premium yang perlu login, non-premium tidak perlu login
$cta_link = $is_premium ? basis_url('member_dashboard.php') : basis_url('member_register.php');
$cta_label = $is_premium ? 'Masuk Dashboard Premium' : 'Daftar Member Premium';
?>

<section class="hero d-flex align-items-center">
	<div class="container mt-5">
		<div class="row align-items-center">
			<div class="col-lg-7" data-aos="fade-right">
				<?php if ($pesan_error): ?>
					<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
						<i class="fa-solid fa-info-circle me-2"></i><?= aman_html($pesan_error) ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				<?php endif; ?>
				<?php if ($is_premium): ?>
					<span class="badge bg-success mb-3">Anda sudah premium</span>
				<?php endif; ?>
				<h1 class="display-4 fw-bold mb-3">Bangun Konsistensi, Raih <span class="teks-emas">Kebebasan</span>.</h1>
				<p class="lead text-secondary mb-4">Saya <span class="teks-emas fw-semibold">ARFXTRADE</span>, trader profesional yang berfokus pada disiplin, manajemen risiko, dan probabilitas. Mari tumbuhkan aset secara berkelanjutan.</p>
				<div class="mb-4 text-center">
					<a href="https://t.me/spaceidfx" target="_blank" rel="noopener noreferrer" class="btn btn-warning btn-lg px-5 py-3 d-inline-block" style="font-size: 1.3rem; font-weight: 700; min-width: 350px;">
						<i class="fa-brands fa-telegram me-2" style="font-size: 1.5rem;"></i>JOIN CIRCLE ARFXTRADE
					</a>
				</div>
				<div class="d-flex flex-wrap gap-3">
					<a href="<?= aman_html($cta_link) ?>" class="btn btn-emas btn-lg px-4">
						<i class="fa-solid fa-crown me-2"></i><?= aman_html($cta_label) ?>
					</a>
					<?php if (!$is_premium): ?>
					<a href="<?= aman_html(basis_url('login.php')) ?>" class="btn btn-outline-light btn-lg px-4 border-emas">
						<i class="fa-solid fa-sign-in-alt me-2"></i>Login
					</a>
					<?php endif; ?>
					<a href="<?= aman_html(basis_url('portofolio.php')) ?>" class="btn btn-outline-light btn-lg px-4 border-emas">
						<i class="fa-solid fa-chart-line me-2"></i>Lihat Portofolio
					</a>
					<?php if (!$is_premium): ?>
					<a href="<?= aman_html(basis_url('edukasi_analisis.php')) ?>" class="btn btn-outline-light btn-lg px-4">
						<i class="fa-solid fa-layer-group me-2"></i>Lihat Struktur Modul
					</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-lg-5 mt-5 mt-lg-0" data-aos="fade-left">
				<div class="kartu-gelap p-4 rounded-4 shadow-lg position-relative">
					<div class="text-center mb-4" style="background: #000; padding: 2rem 1.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; min-height: 400px; width: 100%; overflow: hidden;">
						<img src="<?= aman_html(basis_url('aset/image/logo_arfx.jpeg')) ?>" alt="ARFXTRADE Logo" style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges; filter: contrast(1.1) brightness(1.05);" onerror="this.style.display='none';">
					</div>
					<div class="mb-4">
						<small class="text-uppercase text-muted">Snapshot Komunitas</small>
						<h3 class="fw-semibold mt-2 teks-emas">30 Juta → 10 Miliar</h3>
						<p class="text-secondary mb-0">Porto transparan, setup terdokumentasi, disiplin money management.</p>
					</div>
					<ul class="list-unstyled mb-0">
						<li class="d-flex align-items-center mb-3">
							<span class="badge bg-success bg-opacity-10 text-success me-3 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="fa-solid fa-bolt"></i></span>
							<span>Dashboard monitoring dengan catatan entry/exit.</span>
						</li>
						<li class="d-flex align-items-center mb-3">
							<span class="badge bg-info bg-opacity-10 text-info me-3 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="fa-solid fa-people-group"></i></span>
							<span>Komunitas aktif dengan diskusi real-time setiap hari.</span>
						</li>
						<li class="d-flex align-items-center">
							<span class="badge bg-warning bg-opacity-10 text-warning me-3 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="fa-solid fa-clipboard-check"></i></span>
							<span>Framework 3 fase: riset, eksekusi, evaluasi.</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="py-5">
	<div class="container">
		<div class="row g-4">
			<div class="col-md-4" data-aos="zoom-in-up">
				<div class="kartu-gelap p-4 h-100">
					<i class="fa-solid fa-shield-halved teks-emas fa-2x mb-3"></i>
					<h5 class="fw-semibold mb-2">Disiplin & Manajemen Risiko</h5>
					<p class="text-secondary mb-0">Mengutamakan perlindungan modal melalui sizing terukur, stop loss, dan risk-reward yang jelas.</p>
				</div>
			</div>
			<div class="col-md-4" data-aos="zoom-in-up" data-aos-delay="100">
				<div class="kartu-gelap p-4 h-100">
					<i class="fa-solid fa-magnifying-glass-chart teks-emas fa-2x mb-3"></i>
					<h5 class="fw-semibold mb-2">Analisis Probabilitas</h5>
					<p class="text-secondary mb-0">Menggabungkan struktur pasar, likuiditas, dan momentum untuk mengambil peluang berprobabilitas tinggi.</p>
				</div>
			</div>
			<div class="col-md-4" data-aos="zoom-in-up" data-aos-delay="200">
				<div class="kartu-gelap p-4 h-100">
					<i class="fa-solid fa-seedling teks-emas fa-2x mb-3"></i>
					<h5 class="fw-semibold mb-2">Pertumbuhan Berkelanjutan</h5>
					<p class="text-secondary mb-0">Fokus pada konsistensi jangka panjang, bukan hasil sesaat. Akumulasi kecil yang disiplin.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if (!$is_premium): ?>
<section class="py-5 bg-body-tertiary">
	<div class="container">
		<div class="row align-items-center g-5">
			<div class="col-lg-5">
				<p class="text-uppercase text-muted mb-2"><?= $is_logged_in ? 'Status Akun Anda' : 'Kenapa Harus Gabung?' ?></p>
				<h2 class="fw-bold mb-3"><?= $is_logged_in ? 'Ringkasan Membership Saat Ini' : 'Preview Benefit Premium' ?></h2>
				<?php if ($is_logged_in && $member_info): ?>
					<div class="kartu-gelap p-4 mb-3">
						<div class="d-flex justify-content-between">
							<div>
								<small class="text-muted">Nama</small>
								<div class="fw-semibold"><?= aman_html($member_info['nama_lengkap'] ?? 'Member') ?></div>
							</div>
							<div class="text-end">
								<small class="text-muted">Status</small>
								<span class="badge bg-warning text-dark"><?= aman_html(ucfirst($member_info['status_member'])) ?></span>
							</div>
						</div>
						<hr>
						<div class="row g-3">
							<div class="col-12">
								<small class="text-muted d-block">Membership</small>
								<div class="fw-semibold teks-emas">
									<i class="fa-solid fa-infinity me-1"></i>Premium Lifetime
								</div>
								<small class="text-success">Akses seumur hidup</small>
							</div>
						</div>
					</div>
				<?php else: ?>
					<p class="text-secondary mb-3">Nikmati akses ke analisa real-time, signal siap pakai, serta community review yang terdokumentasi.</p>
				<?php endif; ?>
				<a href="<?= aman_html($cta_link) ?>" class="btn btn-emas mt-2">
					<i class="fa-solid fa-crown me-2"></i><?= aman_html($cta_label) ?>
				</a>
			</div>
			<div class="col-lg-7">
				<div class="row g-4">
					<?php foreach ($benefits as $benefit): ?>
					<div class="col-md-4">
						<div class="kartu-gelap p-4 h-100 rounded-4">
							<h5 class="fw-semibold mb-2"><?= aman_html($benefit['judul']) ?></h5>
							<p class="text-secondary small mb-0"><?= aman_html($benefit['deskripsi']) ?></p>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="py-5">
	<div class="container">
		<div class="text-center mb-5">
			<p class="text-uppercase text-muted mb-2">Preview Modul</p>
			<h2 class="fw-bold">Fitur Premium yang Akan Anda Buka</h2>
		</div>
		<div class="row g-4">
			<?php foreach ($preview_modules as $module): ?>
			<div class="col-lg-3 col-md-6">
				<div class="kartu-gelap p-4 h-100 position-relative">
					<div class="position-absolute top-0 end-0 m-3">
						<span class="badge bg-warning text-dark">Premium</span>
					</div>
					<i class="fa-solid <?= $module['ikon'] ?> teks-emas fa-3x mb-3"></i>
					<h5 class="fw-semibold mb-2"><?= aman_html($module['judul']) ?></h5>
					<p class="text-secondary small mb-4"><?= aman_html($module['deskripsi']) ?></p>
					<a class="btn btn-outline-light btn-sm disabled" href="#">
						<i class="fa-solid fa-lock me-1"></i>Terkunci
					</a>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-5 bg-dark text-light">
	<div class="container">
		<div class="row g-4 align-items-center">
			<div class="col-lg-5">
				<h2 class="fw-bold mb-3">Langkah Upgrade</h2>
				<p class="text-secondary">Proses aktivasi premium dibuat sederhana. Setelah pembayaran diverifikasi, akses dashboard premium terbuka otomatis.</p>
			</div>
			<div class="col-lg-7">
				<div class="row g-4">
					<?php foreach ($langkah_upgrade as $langkah): ?>
					<div class="col-md-4">
						<div class="kartu-gelap p-4 h-100 text-center">
							<div class="badge bg-emas text-dark rounded-circle mb-3" style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;">
								<?= aman_html($langkah['step']) ?>
							</div>
							<h6 class="fw-semibold mb-2"><?= aman_html($langkah['judul']) ?></h6>
							<p class="text-secondary small mb-0"><?= aman_html($langkah['deskripsi']) ?></p>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="py-5 bg-body-tertiary">
	<div class="container text-center">
		<h2 class="fw-bold mb-3">Siap Masuk Dashboard Premium?</h2>
		<p class="text-secondary mb-4">Dapatkan akses penuh ke analisa, signal, dan tools eksklusif. Kami bantu langkahnya.</p>
		<div class="d-flex flex-wrap justify-content-center gap-3">
			<a href="<?= aman_html($cta_link) ?>" class="btn btn-emas btn-lg">
				<i class="fa-solid fa-crown me-2"></i><?= aman_html($cta_label) ?>
			</a>
			<a href="<?= aman_html(basis_url('kontak.php')) ?>" class="btn btn-outline-dark btn-lg">
				<i class="fa-solid fa-comments me-2"></i>Hubungi Admin
			</a>
		</div>
	</div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>







