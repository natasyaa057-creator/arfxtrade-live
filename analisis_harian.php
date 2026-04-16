<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/premium_guard.php';
premiumOnly(); // Proteksi premium

$judul_halaman = 'Analisa Harian - ARFXTRADE Premium';
$deskripsi_meta = 'Analisa pasar forex harian eksklusif untuk member premium ARFXTRADE.';
require_once __DIR__ . '/includes/kepala.php';

// Get analisa harian dari database
$tanggal_hari_ini = date('Y-m-d');
$sql = "SELECT * FROM analisis WHERE tanggal = ? ORDER BY id_analisis DESC LIMIT 10";
$stmt = jalankan_query_siap($sql, 's', [$tanggal_hari_ini]);
$analisa_harian = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold">Analisa <span class="teks-emas">Harian</span></h1>
                <p class="text-secondary mb-0">Analisa pasar forex eksklusif untuk member premium</p>
            </div>
            <a href="<?= aman_html(basis_url('member_dashboard.php')) ?>" class="btn btn-outline-light">
                <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
        
        <?php if (empty($analisa_harian)): ?>
            <div class="kartu-gelap p-5 text-center">
                <i class="fa-solid fa-chart-line teks-emas fa-4x mb-3"></i>
                <h4 class="fw-bold mb-3">Belum Ada Analisa Hari Ini</h4>
                <p class="text-secondary">Analisa akan dipublikasikan segera.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($analisa_harian as $analisa): ?>
                    <div class="col-12">
                        <div class="kartu-gelap p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="fw-bold mb-2"><?= aman_html($analisa['judul']) ?></h4>
                                    <span class="badge bg-emas text-dark"><?= aman_html($analisa['pasangan_mata_uang']) ?></span>
                                </div>
                                <small class="text-muted"><?= date('d F Y', strtotime($analisa['tanggal'])) ?></small>
                            </div>
                            
                            <?php if ($analisa['gambar']): ?>
                                <img src="<?= aman_html(basis_url($analisa['gambar'])) ?>" 
                                     class="img-fluid rounded mb-3" alt="<?= aman_html($analisa['judul']) ?>">
                            <?php endif; ?>
                            
                            <div class="analisa-content">
                                <?= nl2br(aman_html($analisa['isi_analisis'])) ?>
                            </div>
                            
                            <div class="mt-3">
                                <a href="<?= aman_html(basis_url('detail_analisis.php?id=' . $analisa['id_analisis'])) ?>" 
                                   class="btn btn-sm btn-outline-light">
                                    <i class="fa-solid fa-eye me-1"></i>Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>





