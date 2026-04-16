<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/notifikasi.php';

$judul_halaman = 'Verifikasi Membership - ARFXTRADE';
$deskripsi_meta = 'Dashboard admin untuk verifikasi membership member premium.';
require_once __DIR__ . '/../includes/kepala.php';

$pesan_sukses = '';
$pesan_error = '';

// Proses verifikasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id_member = (int)($_POST['id_member'] ?? 0);
    $id_payment = (int)($_POST['id_payment'] ?? 0);
    $alasan_penolakan = trim($_POST['alasan_penolakan'] ?? '');
    // Catatan: Premium sekarang LIFETIME, tidak ada paket, expired date, atau status
    if ($action === 'approve' && $id_member > 0 && $id_payment > 0) {
        // Get payment info
        $sql_payment = "SELECT * FROM payment_proof WHERE id_payment = ?";
        $stmt_payment = jalankan_query_siap($sql_payment, 'i', [$id_payment]);
        $payment = $stmt_payment->get_result()->fetch_assoc();
        $stmt_payment->close();
        
        if ($payment) {
            // Set tanggal untuk premium membership (sekali bayar - LIFETIME)
            $tanggal_mulai = date('Y-m-d');
            
            // Update status member menjadi active
            $sql_member = "UPDATE member SET status_member = 'active' WHERE id_member = ?";
            $stmt_member = jalankan_query_siap($sql_member, 'i', [$id_member]);
            $stmt_member->close();
            
            // Insert membership (Premium Lifetime - tidak ada paket, expired, atau status)
            $sql_membership = "INSERT INTO membership (id_member, tanggal_mulai, tanggal_aktivasi) 
                              VALUES (?, ?, NOW())";
            $stmt_membership = jalankan_query_siap($sql_membership, 'is', 
                [$id_member, $tanggal_mulai]);
            $stmt_membership->close();
            
            // Update payment proof status
            $sql_update_payment = "UPDATE payment_proof 
                                   SET status_verifikasi = 'approved', 
                                       diverifikasi_oleh = ?, 
                                       diverifikasi_pada = NOW() 
                                   WHERE id_payment = ?";
            $stmt_update_payment = jalankan_query_siap($sql_update_payment, 'ii', 
                [$_SESSION['admin_id'], $id_payment]);
            $stmt_update_payment->close();
            
            // Kirim notifikasi
            kirim_notifikasi_verifikasi_berhasil($id_member);
            
            $pesan_sukses = 'Membership berhasil diverifikasi dan diaktifkan.';
        }
    } elseif ($action === 'reject' && $id_member > 0 && $id_payment > 0) {
        if (empty($alasan_penolakan)) {
            $pesan_error = 'Alasan penolakan harus diisi.';
        } else {
            // Update status member menjadi rejected
            $sql_member = "UPDATE member SET status_member = 'rejected' WHERE id_member = ?";
            $stmt_member = jalankan_query_siap($sql_member, 'i', [$id_member]);
            $stmt_member->close();
            
            // Update payment proof status
            $sql_update_payment = "UPDATE payment_proof 
                                   SET status_verifikasi = 'rejected', 
                                       alasan_penolakan = ?,
                                       diverifikasi_oleh = ?, 
                                       diverifikasi_pada = NOW() 
                                   WHERE id_payment = ?";
            $stmt_update_payment = jalankan_query_siap($sql_update_payment, 'sii', 
                [$alasan_penolakan, $_SESSION['admin_id'], $id_payment]);
            $stmt_update_payment->close();
            
            // Kirim notifikasi
            kirim_notifikasi_verifikasi_ditolak($id_member, $alasan_penolakan);
            
            $pesan_sukses = 'Membership ditolak dan notifikasi telah dikirim.';
        }
    }
}

// Get pending members
$sql_pending = "SELECT m.*, pp.id_payment, pp.file_bukti, pp.dibuat_pada as tanggal_daftar
                FROM member m
                JOIN payment_proof pp ON m.id_member = pp.id_member
                WHERE m.status_member = 'pending' AND pp.status_verifikasi = 'pending'
                ORDER BY pp.dibuat_pada DESC";
$stmt_pending = jalankan_query_siap($sql_pending, '', []);
$pending_members = $stmt_pending->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_pending->close();

// Get active members
$sql_active = "SELECT m.*, mem.tanggal_mulai, mem.tanggal_aktivasi
               FROM member m
               JOIN membership mem ON m.id_member = mem.id_member
               WHERE m.status_member = 'active'
               ORDER BY mem.tanggal_aktivasi DESC";
$stmt_active = jalankan_query_siap($sql_active, '', []);
$active_members = $stmt_active->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_active->close();
?>

<div class="container mt-5 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Verifikasi <span class="teks-emas">Membership</span></h1>
        <a href="<?= aman_html(basis_url('dashboard.php')) ?>" class="btn btn-outline-light">
            <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Dashboard
        </a>
    </div>
    
    <?php if ($pesan_sukses): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i><?= aman_html($pesan_sukses) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($pesan_error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-triangle me-2"></i><?= aman_html($pesan_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                Pending <span class="badge bg-warning"><?= count($pending_members) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button">
                Member Aktif <span class="badge bg-success"><?= count($active_members) ?></span>
            </button>
        </li>
    </ul>
    
    <div class="tab-content">
        <!-- Tab Pending -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <?php if (empty($pending_members)): ?>
                <div class="kartu-gelap p-5 text-center">
                    <i class="fa-solid fa-check-circle text-success fa-3x mb-3"></i>
                    <p class="text-secondary">Tidak ada pendaftar yang menunggu verifikasi.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($pending_members as $member): ?>
                        <div class="col-lg-6">
                            <div class="kartu-gelap p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1"><?= aman_html($member['nama_lengkap']) ?></h5>
                                        <p class="text-secondary small mb-0">
                                            <i class="fa-solid fa-user me-1"></i><?= aman_html($member['username']) ?>
                                        </p>
                                    </div>
                                    <span class="badge bg-warning">Pending</span>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block">Email:</small>
                                    <div><?= aman_html($member['email']) ?></div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block">WhatsApp:</small>
                                    <div><?= aman_html($member['nomor_whatsapp']) ?></div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block">Membership:</small>
                                    <div class="fw-semibold teks-emas">Premium Lifetime (Sekali Bayar)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block">Bukti Pembayaran:</small>
                                    <a href="<?= aman_html(basis_url($member['file_bukti'])) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-light">
                                        <i class="fa-solid fa-image me-1"></i>Lihat Bukti
                                    </a>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Tanggal Daftar:</small>
                                    <div><?= date('d F Y H:i', strtotime($member['tanggal_daftar'])) ?></div>
                                </div>
                                
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="id_member" value="<?= $member['id_member'] ?>">
                                    <input type="hidden" name="id_payment" value="<?= $member['id_payment'] ?>">
                                    
                                    <div class="mb-3">
                                        <div class="alert alert-info alert-sm mb-0">
                                            <small><i class="fa-solid fa-info-circle me-1"></i>Premium = Lifetime (sekali bayar selamanya). Tidak ada expired date.</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3" id="alasan-reject-<?= $member['id_member'] ?>" style="display: none;">
                                        <label class="form-label small">Alasan Penolakan</label>
                                        <textarea class="form-control form-control-sm" name="alasan_penolakan" rows="2"></textarea>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="action" value="approve" 
                                                class="btn btn-success btn-sm flex-fill">
                                            <i class="fa-solid fa-check me-1"></i>Approve
                                        </button>
                                        <button type="button" onclick="toggleReject(<?= $member['id_member'] ?>)" 
                                                class="btn btn-outline-warning btn-sm">
                                            <i class="fa-solid fa-times me-1"></i>Tolak
                                        </button>
                                        <button type="submit" name="action" value="reject" 
                                                id="btn-reject-<?= $member['id_member'] ?>" 
                                                style="display: none;"
                                                class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-times me-1"></i>Konfirmasi Tolak
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Tab Active -->
        <div class="tab-pane fade" id="active" role="tabpanel">
            <?php if (empty($active_members)): ?>
                <div class="kartu-gelap p-5 text-center">
                    <p class="text-secondary">Belum ada member aktif.</p>
                </div>
            <?php else: ?>
                <div class="kartu-gelap p-4">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Tanggal Aktivasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_members as $member): ?>
                                    <tr>
                                        <td><?= aman_html($member['nama_lengkap']) ?></td>
                                        <td><?= aman_html($member['email']) ?></td>
                                        <td><?= date('d M Y', strtotime($member['tanggal_mulai'])) ?></td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fa-solid fa-infinity me-1"></i>Premium Lifetime
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Catatan: Modal Edit Expired dihapus karena Premium = LIFETIME -->

<script>
function toggleReject(id) {
    const alasanDiv = document.getElementById('alasan-reject-' + id);
    const btnReject = document.getElementById('btn-reject-' + id);
    
    if (alasanDiv.style.display === 'none') {
        alasanDiv.style.display = 'block';
        btnReject.style.display = 'inline-block';
    } else {
        alasanDiv.style.display = 'none';
        btnReject.style.display = 'none';
    }
}

// Fungsi editExpired dihapus karena Premium = LIFETIME (tidak ada expired date)
</script>

<?php require_once __DIR__ . '/../includes/kaki.php'; ?>

