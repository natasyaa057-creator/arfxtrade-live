<?php
declare(strict_types=1);
$judul_halaman = 'Login - ARFXTRADE';
$deskripsi_meta = 'Login sebagai admin atau member premium ARFXTRADE.';
require_once __DIR__ . '/includes/kepala.php';

$pesan_error = '';
$pesan_sukses = '';
$pesan_info = '';

// Cek pesan dari query string
if (isset($_GET['pesan'])) {
    switch($_GET['pesan']) {
        case 'premium_required':
            $pesan_info = 'Anda harus login sebagai member premium untuk mengakses halaman tersebut.';
            break;
        case 'membership_expired':
            $pesan_info = 'Membership Anda telah expired. Silakan berlangganan kembali.';
            break;
        case 'member_not_found':
            $pesan_info = 'Sesi Anda tidak valid. Silakan login kembali.';
            break;
        case 'logout_sukses':
            $pesan_sukses = 'Anda telah berhasil logout.';
            break;
        default:
            $pesan_info = '';
            break;
    }
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/rate_limiter.php';
    
    $email = trim($_POST['email'] ?? '');
    $kata_sandi = $_POST['kata_sandi'] ?? '';
    $token_csrf = $_POST['token_csrf'] ?? '';
    
    // Cek rate limit
    $rate_check = RateLimiter::checkLimit('login_' . $email, 5, 900);
    
    if (!verifikasi_csrf($token_csrf)) {
        $pesan_error = 'Token keamanan tidak valid.';
    } elseif (!$rate_check['allowed']) {
        $pesan_error = $rate_check['message'];
    } elseif (empty($email) || empty($kata_sandi)) {
        $pesan_error = 'Email/Username dan kata sandi harus diisi.';
    } else {
        try {
            // Cek ADMIN dulu
            $sql_admin = "SELECT id_pengguna, nama_pengguna, email, kata_sandi, peran FROM pengguna WHERE (email = ? OR nama_pengguna = ?) AND peran = 'admin'";
            $stmt_admin = jalankan_query_siap($sql_admin, 'ss', [$email, $email]);
            $hasil_admin = $stmt_admin->get_result();
            
            if ($hasil_admin->num_rows === 1) {
                // User adalah ADMIN
                $admin = $hasil_admin->fetch_assoc();
                $stmt_admin->close();
                
                if (password_verify($kata_sandi, $admin['kata_sandi'])) {
                    // Reset rate limit karena login berhasil
                    RateLimiter::resetAttempts('login_' . $email);
                    
                    // Regenerate session ID setelah login berhasil
                    session_regenerate_id(true);
                    
                    $_SESSION['admin_id'] = $admin['id_pengguna'];
                    $_SESSION['admin_nama'] = $admin['nama_pengguna'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['created'] = time();
                    
                    header('Location: ' . basis_url('dashboard.php'));
                    exit;
                } else {
                    // Record failed attempt
                    RateLimiter::recordAttempt('login_' . $email);
                    $pesan_error = 'Kata sandi salah.';
                    if (isset($rate_check['remaining_attempts'])) {
                        $pesan_error .= ' Sisa percobaan: ' . $rate_check['remaining_attempts'];
                    }
                }
            } else {
                $stmt_admin->close();
                
                // Cek MEMBER PREMIUM
                $sql_member = "SELECT id_member, nama_lengkap, email, kata_sandi, status_member, username 
                              FROM member WHERE email = ? OR username = ?";
                $stmt_member = jalankan_query_siap($sql_member, 'ss', [$email, $email]);
                $hasil_member = $stmt_member->get_result();
                
                if ($hasil_member->num_rows === 1) {
                    $member = $hasil_member->fetch_assoc();
                    
                    if (password_verify($kata_sandi, $member['kata_sandi'])) {
                        // Reset rate limit karena login berhasil
                        RateLimiter::resetAttempts('login_' . $email);
                        
                        // Cek status member - HANYA PREMIUM AKTIF YANG BISA LOGIN
                        if ($member['status_member'] === 'pending') {
                            $pesan_error = 'Akun Anda masih menunggu verifikasi admin. Setelah verifikasi berhasil, Anda dapat login sebagai member premium.';
                        } elseif ($member['status_member'] === 'rejected') {
                            $pesan_error = 'Akun Anda ditolak. Silakan hubungi admin untuk informasi lebih lanjut.';
                        } elseif ($member['status_member'] === 'expired') {
                            $pesan_error = 'Membership Anda telah expired. Silakan berlangganan kembali untuk mendapatkan akses premium.';
                        } elseif ($member['status_member'] !== 'active') {
                            $pesan_error = 'Akun Anda belum aktif. Hanya member premium aktif yang dapat login.';
                        } else {
                            // Cek apakah membership ada (Premium = LIFETIME, tidak perlu cek status)
                            $sql_membership = "SELECT mem.id_membership
                                              FROM membership mem
                                              WHERE mem.id_member = ?
                                              ORDER BY mem.id_membership DESC
                                              LIMIT 1";
                            $stmt_mem = jalankan_query_siap($sql_membership, 'i', [$member['id_member']]);
                            $hasil_mem = $stmt_mem->get_result();
                            
                            if ($hasil_mem->num_rows === 0) {
                                $stmt_mem->close();
                                $pesan_error = 'Anda belum memiliki membership aktif. Login hanya untuk member premium. Silakan berlangganan terlebih dahulu.';
                            } else {
                                // Semua cek berhasil - member premium aktif
                                $stmt_mem->close();
                                
                                // Regenerate session ID setelah login berhasil
                                session_regenerate_id(true);
                                
                                // Set session
                                $_SESSION['member_id'] = $member['id_member'];
                                $_SESSION['member_nama'] = $member['nama_lengkap'];
                                $_SESSION['member_email'] = $member['email'];
                                $_SESSION['member_username'] = $member['username'];
                                $_SESSION['created'] = time();
                                
                                // Redirect
                                $redirect = $_SESSION['redirect_after_login'] ?? basis_url('member_dashboard.php');
                                unset($_SESSION['redirect_after_login']);
                                header('Location: ' . $redirect);
                                exit;
                            }
                        }
                    } else {
                        // Record failed attempt
                        RateLimiter::recordAttempt('login_' . $email);
                        $pesan_error = 'Kata sandi salah.';
                        if (isset($rate_check['remaining_attempts'])) {
                            $pesan_error .= ' Sisa percobaan: ' . $rate_check['remaining_attempts'];
                        }
                    }
                } else {
                    $pesan_error = 'Email atau username tidak ditemukan.';
                }
                $stmt_member->close();
            }
        } catch (Exception $e) {
            Logger::error('Login exception', [
                'error' => $e->getMessage(),
                'email_or_username' => $email,
                'trace' => $e->getTraceAsString()
            ]);
            
            $pesan_error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            if (ENVIRONMENT === 'development') {
                $pesan_error .= ' Error: ' . $e->getMessage();
            }
        }
    }
}

// Cek jika sudah login (admin atau member)
if (isset($_SESSION['admin_id'])) {
    header('Location: ' . basis_url('dashboard.php'));
    exit;
}

if (isset($_SESSION['member_id'])) {
    header('Location: ' . basis_url('member_dashboard.php'));
    exit;
}
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="kartu-gelap p-5">
                    <div class="text-center mb-4">
                        <i class="fa-brands fa-telegram teks-emas fa-4x mb-3"></i>
                        <h2 class="fw-bold" style="font-size: 2.5rem;">Circle ARFXTRADE</h2>
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
                    
                    <?php if ($pesan_info): ?>
                        <div class="alert alert-info" role="alert">
                            <i class="fa-solid fa-info-circle me-2"></i><?= aman_html($pesan_info) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                        
                        <div class="mb-4">
                            <label for="email" class="form-label" style="font-size: 1.1rem; font-weight: 600;">Email atau Username</label>
                            <input type="text" class="form-control form-control-lg" id="email" name="email" 
                                   value="<?= aman_html($email ?? '') ?>" required autofocus style="font-size: 1.1rem; padding: 0.75rem 1rem;">
                            <small class="text-muted" style="font-size: 0.95rem;">Gunakan email atau username Anda</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="kata_sandi" class="form-label" style="font-size: 1.1rem; font-weight: 600;">Kata Sandi</label>
                            <input type="password" class="form-control form-control-lg" id="kata_sandi" name="kata_sandi" required style="font-size: 1.1rem; padding: 0.75rem 1rem;">
                        </div>
                        
                        <button type="submit" class="btn btn-emas w-100 btn-lg" style="font-size: 1.2rem; padding: 0.875rem 1.5rem; font-weight: 600;">
                            <i class="fa-solid fa-sign-in-alt me-2"></i>Masuk
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-secondary mb-2">Belum punya akun premium?</p>
                        <a href="<?= aman_html(basis_url('member_register.php')) ?>" class="btn btn-outline-light border-emas w-100">
                            <i class="fa-solid fa-crown me-2"></i>Daftar Member Premium
                        </a>
                        <p class="text-muted small mt-3 mb-0">
                            Daftar dan verifikasi untuk mendapatkan akses premium ke analisa harian, signal trading, chart & indikator, dan tools eksklusif.
                        </p>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            <strong>Catatan:</strong> Halaman publik dapat diakses tanpa login. Login hanya diperlukan untuk admin dan member premium.
                        </p>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <a href="<?= aman_html(basis_url('')) ?>" class="text-decoration-none text-secondary">
                            <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>
