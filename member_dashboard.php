<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/premium_guard.php';
premiumOnly();
require_once __DIR__ . '/includes/koneksi.php';
require_once __DIR__ . '/includes/keamanan.php';

$judul_halaman = 'Dashboard Member Premium - ARFXTRADE';
$deskripsi_meta = 'Dashboard member premium ARFXTRADE dengan akses ke analisa, signal, dan tools trading.';
require_once __DIR__ . '/includes/kepala.php';

// Cek login member
if (!isset($_SESSION['member_id'])) {
    header('Location: ' . basis_url('login.php'));
    exit;
}

// Get member info
$member_info = getMemberInfo();
$pesan_error = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_error']);

// Premium sekarang LIFETIME - tidak ada sisa hari atau expired date
?>

<section class="py-5 mt-5">
    <div class="container">
        <?php if ($pesan_error): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-exclamation-triangle me-2"></i><?= aman_html($pesan_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Dashboard <span class="teks-emas">Member Premium</span></h1>
                <p class="text-secondary mb-0">Selamat datang, <?= aman_html($member_info['nama_lengkap'] ?? 'Member') ?>!</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= aman_html(basis_url('member_logout.php')) ?>" class="btn btn-outline-danger">
                    <i class="fa-solid fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
        
        <!-- Status Membership -->
        <div class="row g-4 mb-4">
            <div class="col-md-12">
                <div class="kartu-gelap p-4 border border-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-secondary mb-2">Status Membership</h6>
                            <h4 class="fw-bold text-success mb-2">
                                <i class="fa-solid fa-check-circle me-2"></i>Aktif - Lifetime Premium
                            </h4>
                            <p class="text-muted mb-0">
                                <i class="fa-solid fa-infinity me-1"></i>
                                Akses premium Anda berlaku seumur hidup (sekali bayar)
                            </p>
                        </div>
                        <i class="fa-solid fa-crown text-success fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Menu Premium -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <a href="<?= aman_html(basis_url('analisis_harian.php')) ?>" class="text-decoration-none">
                    <div class="kartu-gelap p-4 text-center h-100 hover-lift">
                        <i class="fa-solid fa-chart-line teks-emas fa-3x mb-3"></i>
                        <h5 class="fw-semibold mb-2">Analisa Harian</h5>
                        <p class="text-secondary small mb-0">Analisa pasar forex harian</p>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="<?= aman_html(basis_url('signal_trading.php')) ?>" class="text-decoration-none">
                    <div class="kartu-gelap p-4 text-center h-100 hover-lift">
                        <i class="fa-solid fa-signal teks-emas fa-3x mb-3"></i>
                        <h5 class="fw-semibold mb-2">Signal Trading</h5>
                        <p class="text-secondary small mb-0">Signal trading real-time</p>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="<?= aman_html(basis_url('chart_indikator.php')) ?>" class="text-decoration-none">
                    <div class="kartu-gelap p-4 text-center h-100 hover-lift">
                        <i class="fa-solid fa-chart-area teks-emas fa-3x mb-3"></i>
                        <h5 class="fw-semibold mb-2">Chart & Indikator</h5>
                        <p class="text-secondary small mb-0">Tools analisis teknikal</p>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="<?= aman_html(basis_url('download_premium.php')) ?>" class="text-decoration-none">
                    <div class="kartu-gelap p-4 text-center h-100 hover-lift">
                        <i class="fa-solid fa-download teks-emas fa-3x mb-3"></i>
                        <h5 class="fw-semibold mb-2">Download</h5>
                        <p class="text-secondary small mb-0">Materi & tools premium</p>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="<?= aman_html(basis_url('live_chat.php')) ?>" class="text-decoration-none">
                    <div class="kartu-gelap p-4 text-center h-100 hover-lift">
                        <i class="fa-solid fa-comments teks-emas fa-3x mb-3"></i>
                        <h5 class="fw-semibold mb-2">Live Chat</h5>
                        <p class="text-secondary small mb-0">Chat online member dan admin</p>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Info Paket -->
        <div class="row">
            <div class="col-12">
                <div class="kartu-gelap p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fa-solid fa-info-circle me-2 teks-emas"></i>Informasi Paket
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <small class="text-muted">Membership</small>
                            <div class="fw-semibold teks-emas">
                                <i class="fa-solid fa-infinity me-1"></i>Premium Lifetime
                            </div>
                            <small class="text-success">Akses seumur hidup - Sekali bayar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(212, 175, 55, 0.2);
}
</style>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>



