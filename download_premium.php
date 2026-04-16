<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/premium_guard.php';
premiumOnly(); // Proteksi premium

$judul_halaman = 'Download Premium - ARFXTRADE Premium';
$deskripsi_meta = 'Download materi dan tools trading eksklusif untuk member premium ARFXTRADE.';
require_once __DIR__ . '/includes/kepala.php';
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold">Download <span class="teks-emas">Premium</span></h1>
                <p class="text-secondary mb-0">Materi dan tools trading eksklusif untuk member premium</p>
            </div>
            <a href="<?= aman_html(basis_url('member_dashboard.php')) ?>" class="btn btn-outline-light">
                <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
        
        <div class="row g-4">
            <div class="col-12">
                <div class="kartu-gelap p-4">
                    <div class="alert alert-info mb-4">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Download Premium</strong> berisi materi edukasi, template trading, dan tools eksklusif.
                    </div>
                    
                    <div class="text-center py-5">
                        <i class="fa-solid fa-download teks-emas fa-5x mb-4"></i>
                        <h4 class="fw-bold mb-3">Download Premium</h4>
                        <p class="text-secondary">Fitur ini sedang dalam pengembangan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>





