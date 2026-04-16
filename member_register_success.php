<?php
declare(strict_types=1);
$judul_halaman = 'Registrasi Berhasil - ARFXTRADE';
$deskripsi_meta = 'Terima kasih telah mendaftar sebagai member premium ARFXTRADE.';
require_once __DIR__ . '/includes/kepala.php';
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="kartu-gelap p-5 text-center">
                    <div class="mb-4">
                        <i class="fa-solid fa-check-circle text-success fa-5x mb-3"></i>
                        <h2 class="fw-bold mb-3">Terima Kasih!</h2>
                        <p class="lead text-secondary mb-4">
                            Akun Anda menunggu verifikasi admin.
                        </p>
                    </div>
                    
                    <div class="alert alert-info text-start mb-4">
                        <h6 class="fw-semibold mb-3"><i class="fa-solid fa-info-circle me-2"></i>Langkah Selanjutnya:</h6>
                        <ol class="mb-0">
                            <li>Admin akan memverifikasi bukti pembayaran Anda</li>
                            <li>Anda akan menerima notifikasi via Email/WhatsApp setelah verifikasi</li>
                            <li>Setelah diverifikasi, Anda dapat login dan mengakses fitur premium</li>
                        </ol>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= aman_html(basis_url('login.php')) ?>" class="btn btn-emas">
                            <i class="fa-solid fa-sign-in-alt me-2"></i>Login Member
                        </a>
                        <a href="<?= aman_html(basis_url('')) ?>" class="btn btn-outline-light">
                            <i class="fa-solid fa-home me-2"></i>Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>





