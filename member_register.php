<?php
declare(strict_types=1);
$judul_halaman = 'Registrasi Member Premium - ARFXTRADE';
$deskripsi_meta = 'Daftar sebagai member premium ARFXTRADE untuk akses analisa, signal, dan tools trading eksklusif.';
require_once __DIR__ . '/includes/kepala.php';

$pesan_error = '';
$pesan_sukses = '';

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/validasi_input.php';
    
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $kata_sandi = $_POST['kata_sandi'] ?? '';
    $kata_sandi_konfirmasi = $_POST['kata_sandi_konfirmasi'] ?? '';
    $nomor_whatsapp = trim($_POST['nomor_whatsapp'] ?? '');
    $token_csrf = $_POST['token_csrf'] ?? '';
    
    // Premium Lifetime - tidak perlu kolom paket
    
    // Validasi CSRF
    if (!verifikasi_csrf($token_csrf)) {
        $pesan_error = 'Token keamanan tidak valid.';
    }
    // Validasi nama lengkap
    elseif (!($valid_nama = validasi_nama_lengkap($nama_lengkap))['valid']) {
        $pesan_error = $valid_nama['message'];
    }
    // Validasi username
    elseif (!($valid_username = validasi_username($username))['valid']) {
        $pesan_error = $valid_username['message'];
    }
    // Validasi email
    elseif (!($valid_email = validasi_email($email))['valid']) {
        $pesan_error = $valid_email['message'];
    }
    // Validasi password
    elseif (!($valid_password = validasi_password($kata_sandi, $kata_sandi_konfirmasi))['valid']) {
        $pesan_error = $valid_password['message'];
    }
    // Validasi nomor WhatsApp
    elseif (!($valid_whatsapp = validasi_nomor_whatsapp($nomor_whatsapp))['valid']) {
        $pesan_error = $valid_whatsapp['message'];
    } else {
        // Gunakan nilai yang sudah divalidasi
        $nama_lengkap = $valid_nama['nama_bersih'];
        $username = $valid_username['username_bersih'];
        $email = $valid_email['email_bersih'];
        $nomor_whatsapp = $valid_whatsapp['nomor_bersih'];
        // Cek apakah email atau username sudah terdaftar
        $sql_cek = "SELECT id_member FROM member WHERE email = ? OR username = ?";
        $stmt_cek = jalankan_query_siap($sql_cek, 'ss', [$email, $username]);
        $hasil_cek = $stmt_cek->get_result();
        
        if ($hasil_cek->num_rows > 0) {
            $pesan_error = 'Email atau username sudah terdaftar.';
            $stmt_cek->close();
        } else {
            $stmt_cek->close();
            
            // Upload bukti pembayaran
            $file_bukti = '';
            if (isset($_FILES['bukti_pembayaran'])) {
                require_once __DIR__ . '/includes/validasi_file.php';
                
                $upload_dir = __DIR__ . '/unggahan/payment_proof/';
                $result = ValidasiFile::uploadBuktiPembayaran($_FILES['bukti_pembayaran'], $upload_dir);
                
                if ($result['success']) {
                    $file_bukti = $result['relative_path'];
                } else {
                    $pesan_error = $result['message'];
                }
            } else {
                $pesan_error = 'Bukti pembayaran harus diupload.';
            }
            
            if (empty($pesan_error)) {
                // Hash password
                $kata_sandi_hash = password_hash($kata_sandi, PASSWORD_DEFAULT);
                
                // Insert member dengan status pending
                try {
                    $sql_member = "INSERT INTO member (nama_lengkap, username, email, kata_sandi, nomor_whatsapp, status_member) 
                                  VALUES (?, ?, ?, ?, ?, 'pending')";
                    $stmt_member = jalankan_query_siap($sql_member, 'sssss', 
                        [$nama_lengkap, $username, $email, $kata_sandi_hash, $nomor_whatsapp]);
                    $id_member = $koneksi->insert_id;
                    $stmt_member->close();
                    
                    // Insert payment proof (Premium Lifetime - tidak ada kolom paket)
                    $sql_payment = "INSERT INTO payment_proof (id_member, file_bukti, status_verifikasi) 
                                   VALUES (?, ?, 'pending')";
                    $stmt_payment = jalankan_query_siap($sql_payment, 'is', [$id_member, $file_bukti]);
                    $stmt_payment->close();
                    
                    // Redirect ke halaman sukses
                    header('Location: ' . basis_url('member_register_success.php'));
                    exit;
                } catch (Exception $e) {
                    $pesan_error = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                    // Hapus file yang sudah diupload jika insert gagal
                    if (!empty($file_bukti) && file_exists(__DIR__ . '/' . $file_bukti)) {
                        @unlink(__DIR__ . '/' . $file_bukti);
                    }
                }
            }
        }
    }
}
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="kartu-gelap p-5">
                    <div class="text-center mb-4">
                        <i class="fa-solid fa-crown teks-emas fa-3x mb-3"></i>
                        <h2 class="fw-bold">Registrasi Member <span class="teks-emas">Premium</span></h2>
                        <p class="text-secondary">Daftar sekarang untuk akses eksklusif ke analisa, signal, dan tools trading</p>
                    </div>
                    
                    <?php if ($pesan_error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i><?= aman_html($pesan_error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($pesan_sukses): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fa-solid fa-check-circle me-2"></i><?= aman_html($pesan_sukses) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                       value="<?= aman_html($nama_lengkap ?? '') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= aman_html($username ?? '') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= aman_html($email ?? '') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="nomor_whatsapp" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomor_whatsapp" name="nomor_whatsapp" 
                                       value="<?= aman_html($nomor_whatsapp ?? '') ?>" 
                                       placeholder="081234567890" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="kata_sandi" class="form-label">Kata Sandi <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="kata_sandi" name="kata_sandi" required>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="kata_sandi_konfirmasi" class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="kata_sandi_konfirmasi" name="kata_sandi_konfirmasi" required>
                            </div>
                            
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="fw-semibold mb-2"><i class="fa-solid fa-crown me-2"></i>Paket Premium</h6>
                                    <p class="mb-1"><strong>Harga:</strong> <span class="teks-emas fw-bold">Rp 1.000.000</span> (Sekali Bayar)</p>
                                    <p class="mb-0"><small class="text-muted">Akses seumur hidup ke semua fitur premium</small></p>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label for="bukti_pembayaran" class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" 
                                       accept="image/*,.pdf" required>
                                <small class="text-muted">Format: JPG, PNG, atau PDF (Max 5MB)</small>
                            </div>
                            
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="fw-semibold mb-2"><i class="fa-solid fa-info-circle me-2"></i>Informasi Pembayaran:</h6>
                                    <p class="mb-2"><strong>Total Pembayaran:</strong> <span class="teks-emas fw-bold">Rp 1.000.000</span> (Sekali Bayar)</p>
                                    <p class="mb-1"><strong>Bank Transfer:</strong> BCA 1234567890 a.n. ARFXTRADE</p>
                                    <p class="mb-0"><strong>E-Wallet:</strong> OVO/DANA/GoPay 081234567890</p>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-emas w-100 btn-lg">
                                    <i class="fa-solid fa-user-plus me-2"></i>Daftar Sekarang
                                </button>
                            </div>
                            
                            <div class="col-12 text-center">
                                <p class="text-secondary mb-0">
                                    Sudah punya akun? <a href="<?= aman_html(basis_url('login.php')) ?>" class="teks-emas">Login di sini</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>





