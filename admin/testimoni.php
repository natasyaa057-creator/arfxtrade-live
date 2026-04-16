<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/admin_auth.php';

$judul_halaman = 'Kelola Testimoni';
$deskripsi_meta = 'Kelola testimoni klien ARFXTRADE.';
require_once __DIR__ . '/../includes/kepala.php';

$pesan_sukses = '';
$pesan_error = '';

// Proses aksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id_testimoni = (int)($_POST['id_testimoni'] ?? 0);
    $token_csrf = $_POST['token_csrf'] ?? '';
    
    if (!verifikasi_csrf($token_csrf)) {
        $pesan_error = 'Token keamanan tidak valid.';
    } elseif ($aksi === 'setujui' && $id_testimoni > 0) {
        try {
            $sql = "UPDATE testimoni SET tampil = 1 WHERE id_testimoni = ?";
            $stmt = jalankan_query_siap($sql, 'i', [$id_testimoni]);
            $stmt->close();
            $pesan_sukses = 'Testimoni berhasil disetujui.';
        } catch (Exception $e) {
            $pesan_error = 'Gagal menyetujui testimoni.';
        }
    } elseif ($aksi === 'hapus' && $id_testimoni > 0) {
        try {
            $sql = "DELETE FROM testimoni WHERE id_testimoni = ?";
            $stmt = jalankan_query_siap($sql, 'i', [$id_testimoni]);
            $stmt->close();
            $pesan_sukses = 'Testimoni berhasil dihapus.';
        } catch (Exception $e) {
            $pesan_error = 'Gagal menghapus testimoni.';
        }
    }
}

// Ambil semua testimoni
$testimoni_data = [];
try {
    $sql = "SELECT * FROM testimoni ORDER BY tanggal DESC";
    $stmt = jalankan_query_siap($sql, '', []);
    $hasil = $stmt->get_result();
    $testimoni_data = $hasil->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $testimoni_data = [];
}
?>

<div class="container mt-5 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Kelola <span class="teks-emas">Testimoni</span></h1>
        <div class="d-flex gap-2">
            <a href="<?= aman_html(basis_url('dashboard.php')) ?>" class="btn btn-outline-light">
                <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
            </a>
        </div>
    </div>
    
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
    
    <div class="kartu-gelap p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Testimoni</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimoni_data as $testimoni): ?>
                        <tr>
                            <td><?= $testimoni['id_testimoni'] ?></td>
                            <td><?= aman_html($testimoni['nama']) ?></td>
                            <td><?= aman_html(substr($testimoni['isi_testimoni'], 0, 100)) ?>...</td>
                            <td><?= date('d M Y H:i', strtotime($testimoni['tanggal'])) ?></td>
                            <td>
                                <?php if ($testimoni['tampil']): ?>
                                    <span class="badge bg-success">Tampil</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if (!$testimoni['tampil']): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                                            <input type="hidden" name="aksi" value="setujui">
                                            <input type="hidden" name="id_testimoni" value="<?= $testimoni['id_testimoni'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('Setujui testimoni ini?')">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="id_testimoni" value="<?= $testimoni['id_testimoni'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Hapus testimoni ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($testimoni_data)): ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-comments teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-2">Belum Ada Testimoni</h5>
                <p class="text-secondary">Testimoni akan muncul di sini setelah ada yang mengirim.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/kaki.php'; ?>








