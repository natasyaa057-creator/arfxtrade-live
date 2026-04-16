<?php
declare(strict_types=1);
$judul_halaman = 'Kontak';
$deskripsi_meta = 'Hubungi ARFXTRADE untuk kolaborasi, konsultasi trading, atau pertanyaan lainnya.';
require_once __DIR__ . '/includes/kepala.php';

$pesan_sukses = '';
$pesan_error = '';

// Proses form kontak
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    $token_csrf = $_POST['token_csrf'] ?? '';
    
    if (!verifikasi_csrf($token_csrf)) {
        $pesan_error = 'Token keamanan tidak valid.';
    } elseif (empty($nama) || empty($email) || empty($subjek) || empty($pesan)) {
        $pesan_error = 'Semua field harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan_error = 'Format email tidak valid.';
    } else {
        // Simpan pesan ke database (opsional)
        try {
            $sql = "INSERT INTO testimoni (nama, isi_testimoni) VALUES (?, ?)";
            $isi_pesan = "Kontak dari: $email\nSubjek: $subjek\n\nPesan:\n$pesan";
            $stmt = jalankan_query_siap($sql, 'ss', [$nama, $isi_pesan]);
            $stmt->close();
        } catch (Exception $e) {
            // Abaikan jika gagal simpan ke database
        }
        
        // Kirim email (simulasi)
        $pesan_sukses = 'Pesan berhasil dikirim! Kami akan segera menghubungi Anda.';
    }
}
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h1 class="display-5 fw-bold mb-3">Hubungi <span class="teks-emas">ARFXTRADE</span></h1>
            <p class="lead text-secondary">Mari diskusikan peluang kolaborasi dan konsultasi trading</p>
        </div>
        
        <div class="row g-5">
            <div class="col-lg-8" data-aos="fade-right">
                <div class="kartu-gelap p-5">
                    <h4 class="fw-bold mb-4">Kirim Pesan</h4>
                    
                    <?php if ($pesan_sukses): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fa-solid fa-check-circle me-2"></i><?= aman_html($pesan_sukses) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($pesan_error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i><?= aman_html($pesan_error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?= aman_html($_POST['nama'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= aman_html($_POST['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="subjek" class="form-label">Subjek</label>
                                <input type="text" class="form-control" id="subjek" name="subjek" 
                                       value="<?= aman_html($_POST['subjek'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="pesan" class="form-label">Pesan</label>
                                <textarea class="form-control" id="pesan" name="pesan" rows="6" required><?= aman_html($_POST['pesan'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-emas btn-lg">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Kirim Pesan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-left">
                <div class="kartu-gelap p-4 mb-4">
                    <h5 class="fw-bold teks-emas mb-3">Informasi Kontak</h5>
                    <div class="text-center">
                        <a href="https://t.me/spaceidfx" target="_blank" rel="noopener noreferrer" class="btn btn-warning btn-lg px-5 py-3 d-inline-block" style="font-size: 1.3rem; font-weight: 700; min-width: 350px;">
                            <i class="fa-brands fa-telegram me-2" style="font-size: 1.5rem;"></i>JOIN CIRCLE ARFXTRADE
                        </a>
                    </div>
                </div>
                
                <div class="kartu-gelap p-4">
                    <h5 class="fw-bold teks-emas mb-3">Jam Kerja</h5>
                    <div>
                        <strong>Senin - Jumat:</strong><br>
                        <span class="text-secondary">09:00 - 22:00 WIB</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>








