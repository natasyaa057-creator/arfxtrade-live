<?php
declare(strict_types=1);
$judul_halaman = 'Testimoni & Kolaborasi';
$deskripsi_meta = 'Testimoni dan kolaborasi ARFXTRADE - pengalaman klien, kerjasama trading, dan kesempatan kolaborasi untuk trader profesional.';
require_once __DIR__ . '/includes/kepala.php';

$pesan_sukses = '';
$pesan_error = '';

// Proses submit testimoni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimoni'])) {
    $nama = trim($_POST['nama'] ?? '');
    $isi_testimoni = trim($_POST['isi_testimoni'] ?? '');
    $token_csrf = $_POST['token_csrf'] ?? '';
    
    if (!verifikasi_csrf($token_csrf)) {
        $pesan_error = 'Token keamanan tidak valid.';
    } elseif (empty($nama) || empty($isi_testimoni)) {
        $pesan_error = 'Nama dan testimoni harus diisi.';
    } else {
        try {
            $sql = "INSERT INTO testimoni (nama, isi_testimoni) VALUES (?, ?)";
            $stmt = jalankan_query_siap($sql, 'ss', [$nama, $isi_testimoni]);
            $stmt->close();
            $pesan_sukses = 'Testimoni berhasil dikirim! Terima kasih atas feedback Anda.';
        } catch (Exception $e) {
            $pesan_error = 'Gagal mengirim testimoni. Silakan coba lagi.';
        }
    }
}

// Ambil testimoni yang sudah disetujui
$testimoni_data = [];
try {
    $sql = "SELECT * FROM testimoni WHERE tampil = 1 ORDER BY tanggal DESC LIMIT 10";
    $stmt = jalankan_query_siap($sql, '', []);
    $hasil = $stmt->get_result();
    $testimoni_data = $hasil->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    // Jika tabel belum ada, gunakan data dummy
    $testimoni_data = [
        [
            'id_testimoni' => 1,
            'nama' => 'Ahmad Rizki',
            'isi_testimoni' => 'ARFXTRADE memberikan analisis yang sangat akurat dan membantu saya meningkatkan profit trading. Metode money management yang diajarkan sangat berguna.',
            'tanggal' => '2024-01-15 10:30:00'
        ],
        [
            'id_testimoni' => 2,
            'nama' => 'Sarah Putri',
            'isi_testimoni' => 'Sebagai trader pemula, saya sangat terbantu dengan edukasi yang diberikan. Penjelasannya mudah dipahami dan praktis.',
            'tanggal' => '2024-01-12 14:20:00'
        ],
        [
            'id_testimoni' => 3,
            'nama' => 'Budi Santoso',
            'isi_testimoni' => 'Kolaborasi dengan ARFXTRADE sangat profesional. Analisis pasar harian sangat membantu dalam pengambilan keputusan trading.',
            'tanggal' => '2024-01-10 09:15:00'
        ]
    ];
}
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h1 class="display-5 fw-bold mb-3">Testimoni & <span class="teks-emas">Kolaborasi</span></h1>
            <p class="lead text-secondary">Pengalaman klien dan kesempatan kolaborasi</p>
        </div>
        
        <!-- Form Testimoni -->
        <div class="row mb-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="kartu-gelap p-4">
                    <h4 class="fw-bold mb-3">Berikan Testimoni</h4>
                    <p class="text-secondary mb-4">Bagikan pengalaman Anda bekerja sama dengan ARFXTRADE</p>
                    
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
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?= aman_html($_POST['nama'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="isi_testimoni" class="form-label">Testimoni</label>
                            <textarea class="form-control" id="isi_testimoni" name="isi_testimoni" 
                                      rows="4" required><?= aman_html($_POST['isi_testimoni'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" name="submit_testimoni" class="btn btn-emas w-100">
                            <i class="fa-solid fa-paper-plane me-2"></i>Kirim Testimoni
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="kartu-gelap p-4">
                    <h4 class="fw-bold mb-3">Kolaborasi</h4>
                    <p class="text-secondary mb-4">Mari bekerja sama untuk mencapai kesuksesan trading</p>
                    
                    <div class="mb-4">
                        <h6 class="fw-semibold teks-emas mb-2">Jenis Kolaborasi:</h6>
                        <ul class="list-unstyled text-secondary">
                            <li class="mb-2"><i class="fa-solid fa-check me-2"></i>Analisis Pasar Bersama</li>
                            <li class="mb-2"><i class="fa-solid fa-check me-2"></i>Edukasi Trading</li>
                            <li class="mb-2"><i class="fa-solid fa-check me-2"></i>Strategi Development</li>
                            <li class="mb-2"><i class="fa-solid fa-check me-2"></i>Mentoring Program</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-semibold teks-emas mb-2">Kontak Kolaborasi:</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fa-solid fa-envelope me-2"></i>
                            <span>arfxtrade@email.com</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fa-brands fa-whatsapp me-2"></i>
                            <span>+62 812-3456-7890</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fa-brands fa-telegram me-2"></i>
                            <span>@arfxtrade</span>
                        </div>
                    </div>
                    
                    <a href="mailto:arfxtrade@email.com" class="btn btn-outline-light w-100">
                        <i class="fa-solid fa-envelope me-2"></i>Hubungi untuk Kolaborasi
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Daftar Testimoni -->
        <div class="row" data-aos="fade-up">
            <div class="col-12">
                <h4 class="fw-bold mb-4">Testimoni Klien</h4>
                <div class="row g-4">
                    <?php foreach ($testimoni_data as $testimoni): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="kartu-gelap p-4 h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-emas rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fa-solid fa-user text-dark"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-0"><?= aman_html($testimoni['nama']) ?></h6>
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($testimoni['tanggal'])) ?>
                                        </small>
                                    </div>
                                </div>
                                <p class="text-secondary mb-0">
                                    "<?= aman_html($testimoni['isi_testimoni']) ?>"
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($testimoni_data)): ?>
                    <div class="text-center py-4">
                        <div class="kartu-gelap p-4">
                            <i class="fa-solid fa-comments teks-emas fa-2x mb-3"></i>
                            <h5 class="fw-semibold mb-2">Belum Ada Testimoni</h5>
                            <p class="text-secondary">Jadilah yang pertama memberikan testimoni!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>








