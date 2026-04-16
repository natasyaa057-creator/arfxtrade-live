<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin_auth.php';

$judul_halaman = 'Dashboard Admin';
$deskripsi_meta = 'Dashboard admin ARFXTRADE untuk mengelola konten website.';
require_once __DIR__ . '/includes/kepala.php';

// Ambil statistik
$statistik = [
    'total_analisis' => 0,
    'total_portofolio' => 0,
    'total_edukasi' => 0,
    'total_testimoni' => 0,
    'total_pengunjung' => 0,
    'testimoni_pending' => 0
];

try {
    // Hitung total analisis
    $stmt = jalankan_query_siap("SELECT COUNT(*) as total FROM analisis", '', []);
    $statistik['total_analisis'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    
    // Hitung total portofolio
    $stmt = jalankan_query_siap("SELECT COUNT(*) as total FROM portofolio", '', []);
    $statistik['total_portofolio'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    
    // Hitung total edukasi
    $stmt = jalankan_query_siap("SELECT COUNT(*) as total FROM edukasi", '', []);
    $statistik['total_edukasi'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    
    // Hitung total testimoni
    $stmt = jalankan_query_siap("SELECT COUNT(*) as total FROM testimoni", '', []);
    $statistik['total_testimoni'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    
    // Hitung testimoni pending
    $stmt = jalankan_query_siap("SELECT COUNT(*) as total FROM testimoni WHERE tampil = 0", '', []);
    $statistik['testimoni_pending'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    
    // Hitung total pengunjung
    $stmt = jalankan_query_siap("SELECT COUNT(*) as total FROM pengunjung", '', []);
    $statistik['total_pengunjung'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
} catch (Exception $e) {
    // Gunakan data dummy jika tabel belum ada
    $statistik = [
        'total_analisis' => 5,
        'total_portofolio' => 3,
        'total_edukasi' => 4,
        'total_testimoni' => 8,
        'total_pengunjung' => 150,
        'testimoni_pending' => 2
    ];
}

// Ambil testimoni terbaru
$testimoni_terbaru = [];
try {
    $sql = "SELECT * FROM testimoni ORDER BY tanggal DESC LIMIT 5";
    $stmt = jalankan_query_siap($sql, '', []);
    $hasil = $stmt->get_result();
    $testimoni_terbaru = $hasil->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $testimoni_terbaru = [];
}
?>

<div class="container mt-5 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Dashboard <span class="teks-emas">Admin</span></h1>
        <div class="d-flex gap-2">
            <a href="<?= aman_html(basis_url('')) ?>" class="btn btn-outline-light">
                <i class="fa-solid fa-home me-1"></i>Beranda
            </a>
            <a href="<?= aman_html(basis_url('logout.php')) ?>" class="btn btn-outline-danger">
                <i class="fa-solid fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
    
    <!-- Statistik Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up">
            <div class="kartu-gelap p-4 text-center">
                <i class="fa-solid fa-chart-line teks-emas fa-2x mb-3"></i>
                <h3 class="fw-bold mb-1"><?= $statistik['total_analisis'] ?></h3>
                <p class="text-secondary mb-0">Analisis Pasar</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="kartu-gelap p-4 text-center">
                <i class="fa-solid fa-briefcase teks-emas fa-2x mb-3"></i>
                <h3 class="fw-bold mb-1"><?= $statistik['total_portofolio'] ?></h3>
                <p class="text-secondary mb-0">Portofolio</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="kartu-gelap p-4 text-center">
                <i class="fa-solid fa-graduation-cap teks-emas fa-2x mb-3"></i>
                <h3 class="fw-bold mb-1"><?= $statistik['total_edukasi'] ?></h3>
                <p class="text-secondary mb-0">Materi Edukasi</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="300">
            <div class="kartu-gelap p-4 text-center">
                <i class="fa-solid fa-comments teks-emas fa-2x mb-3"></i>
                <h3 class="fw-bold mb-1"><?= $statistik['total_testimoni'] ?></h3>
                <p class="text-secondary mb-0">Testimoni</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="400">
            <div class="kartu-gelap p-4 text-center">
                <i class="fa-solid fa-users teks-emas fa-2x mb-3"></i>
                <h3 class="fw-bold mb-1"><?= $statistik['total_pengunjung'] ?></h3>
                <p class="text-secondary mb-0">Pengunjung</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="500">
            <div class="kartu-gelap p-4 text-center">
                <i class="fa-solid fa-clock teks-emas fa-2x mb-3"></i>
                <h3 class="fw-bold mb-1"><?= $statistik['testimoni_pending'] ?></h3>
                <p class="text-secondary mb-0">Pending</p>
            </div>
        </div>
    </div>
    
    <!-- Menu CRUD -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6" data-aos="fade-up">
            <div class="kartu-gelap p-4 text-center h-100">
                <i class="fa-solid fa-chart-line teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-3">Analisis Pasar</h5>
                <p class="text-secondary small mb-4">Kelola artikel analisis harian</p>
                <a href="<?= aman_html(basis_url('admin/analisis.php')) ?>" class="btn btn-emas w-100">
                    <i class="fa-solid fa-cog me-2"></i>Kelola
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="kartu-gelap p-4 text-center h-100">
                <i class="fa-solid fa-briefcase teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-3">Portofolio</h5>
                <p class="text-secondary small mb-4">Kelola hasil trading</p>
                <a href="<?= aman_html(basis_url('admin/portofolio.php')) ?>" class="btn btn-emas w-100">
                    <i class="fa-solid fa-cog me-2"></i>Kelola
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="kartu-gelap p-4 text-center h-100">
                <i class="fa-solid fa-graduation-cap teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-3">Edukasi</h5>
                <p class="text-secondary small mb-4">Kelola materi pembelajaran</p>
                <a href="<?= aman_html(basis_url('admin/edukasi.php')) ?>" class="btn btn-emas w-100">
                    <i class="fa-solid fa-cog me-2"></i>Kelola
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="kartu-gelap p-4 text-center h-100">
                <i class="fa-solid fa-comments teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-3">Testimoni</h5>
                <p class="text-secondary small mb-4">Kelola testimoni klien</p>
                <a href="<?= aman_html(basis_url('admin/testimoni.php')) ?>" class="btn btn-emas w-100">
                    <i class="fa-solid fa-cog me-2"></i>Kelola
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="kartu-gelap p-4 text-center h-100">
                <i class="fa-solid fa-crown teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-3">Verifikasi Membership</h5>
                <p class="text-secondary small mb-4">Verifikasi member premium</p>
                <a href="<?= aman_html(basis_url('admin/membership_verification.php')) ?>" class="btn btn-emas w-100">
                    <i class="fa-solid fa-cog me-2"></i>Kelola
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="kartu-gelap p-4 text-center h-100">
                <i class="fa-solid fa-comments teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-3">Live Chat</h5>
                <p class="text-secondary small mb-4">Chat online member dan admin</p>
                <a href="<?= aman_html(basis_url('live_chat.php')) ?>" class="btn btn-emas w-100">
                    <i class="fa-solid fa-comment-dots me-2"></i>Buka Chat
                </a>
            </div>
        </div>
    </div>
    
    <!-- Testimoni Terbaru -->
    <div class="row">
        <div class="col-12" data-aos="fade-up">
            <div class="kartu-gelap p-4">
                <h5 class="fw-bold mb-4">Testimoni Terbaru</h5>
                <?php if (!empty($testimoni_terbaru)): ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Testimoni</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($testimoni_terbaru as $testimoni): ?>
                                    <tr>
                                        <td><?= aman_html($testimoni['nama']) ?></td>
                                        <td><?= aman_html(substr($testimoni['isi_testimoni'], 0, 50)) ?>...</td>
                                        <td><?= date('d M Y', strtotime($testimoni['tanggal'])) ?></td>
                                        <td>
                                            <?php if ($testimoni['tampil']): ?>
                                                <span class="badge bg-success">Tampil</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-secondary text-center py-4">Belum ada testimoni.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js untuk statistik -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart statistik (jika diperlukan)
document.addEventListener('DOMContentLoaded', function() {
    // Chart dapat ditambahkan di sini untuk visualisasi data
    console.log('Dashboard loaded');
});
</script>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>




