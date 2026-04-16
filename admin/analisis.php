<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/admin_auth.php';

$judul_halaman = 'Kelola Analisis Pasar';
$deskripsi_meta = 'Kelola artikel analisis pasar ARFXTRADE.';
require_once __DIR__ . '/../includes/kepala.php';

$pesan_sukses = '';
$pesan_error = '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id_analisis = (int)($_POST['id_analisis'] ?? 0);
    $token_csrf = $_POST['token_csrf'] ?? '';
    
    if (!verifikasi_csrf($token_csrf)) {
        $pesan_error = 'Token keamanan tidak valid.';
    } elseif ($aksi === 'tambah' || $aksi === 'edit') {
        $judul = trim($_POST['judul'] ?? '');
        $pasangan_mata_uang = trim($_POST['pasangan_mata_uang'] ?? '');
        $isi_analisis = trim($_POST['isi_analisis'] ?? '');
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        
        if (empty($judul) || empty($pasangan_mata_uang) || empty($isi_analisis)) {
            $pesan_error = 'Semua field harus diisi.';
        } else {
            try {
                $slug = buat_slug($judul);
                
                $gambar = $_POST['gambar_url'] ?? '';
                
                if ($aksi === 'tambah') {
                    $sql = "INSERT INTO analisis (judul, pasangan_mata_uang, isi_analisis, tanggal, slug, gambar) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = jalankan_query_siap($sql, 'ssssss', [$judul, $pasangan_mata_uang, $isi_analisis, $tanggal, $slug, $gambar]);
                    $pesan_sukses = 'Analisis berhasil ditambahkan.';
                } else {
                    $sql = "UPDATE analisis SET judul = ?, pasangan_mata_uang = ?, isi_analisis = ?, tanggal = ?, slug = ?, gambar = ? WHERE id_analisis = ?";
                    $stmt = jalankan_query_siap($sql, 'ssssssi', [$judul, $pasangan_mata_uang, $isi_analisis, $tanggal, $slug, $gambar, $id_analisis]);
                    $pesan_sukses = 'Analisis berhasil diperbarui.';
                }
                $stmt->close();
            } catch (Exception $e) {
                $pesan_error = 'Gagal menyimpan analisis.';
            }
        }
    } elseif ($aksi === 'hapus' && $id_analisis > 0) {
        try {
            $sql = "DELETE FROM analisis WHERE id_analisis = ?";
            $stmt = jalankan_query_siap($sql, 'i', [$id_analisis]);
            $stmt->close();
            $pesan_sukses = 'Analisis berhasil dihapus.';
        } catch (Exception $e) {
            $pesan_error = 'Gagal menghapus analisis.';
        }
    }
}

// Ambil data analisis
$analisis_data = [];
try {
    $sql = "SELECT * FROM analisis ORDER BY tanggal DESC";
    $stmt = jalankan_query_siap($sql, '', []);
    $hasil = $stmt->get_result();
    $analisis_data = $hasil->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $analisis_data = [];
}

// Data untuk form edit
$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    foreach ($analisis_data as $item) {
        if ($item['id_analisis'] == $id_edit) {
            $edit_data = $item;
            break;
        }
    }
}
?>

<div class="container mt-5 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Kelola <span class="teks-emas">Analisis Pasar</span></h1>
        <div class="d-flex gap-2">
            <button class="btn btn-emas" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus me-1"></i>Tambah Analisis
            </button>
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
                        <th>Judul</th>
                        <th>Pasangan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($analisis_data as $analisis): ?>
                        <tr>
                            <td><?= $analisis['id_analisis'] ?></td>
                            <td><?= aman_html(substr($analisis['judul'], 0, 50)) ?>...</td>
                            <td><span class="badge bg-emas text-dark"><?= aman_html($analisis['pasangan_mata_uang']) ?></span></td>
                            <td><?= date('d M Y', strtotime($analisis['tanggal'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="?edit=<?= $analisis['id_analisis'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="id_analisis" value="<?= $analisis['id_analisis'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Hapus analisis ini?')">
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
        
        <?php if (empty($analisis_data)): ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-chart-line teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-2">Belum Ada Analisis</h5>
                <p class="text-secondary">Klik tombol "Tambah Analisis" untuk membuat analisis pertama.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah/Edit Analisis -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content kartu-gelap">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" id="modalTambahLabel">
                    <?= $edit_data ? 'Edit Analisis' : 'Tambah Analisis Baru' ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                    <input type="hidden" name="aksi" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_analisis" value="<?= $edit_data['id_analisis'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Analisis</label>
                        <input type="text" class="form-control" id="judul" name="judul" 
                               value="<?= aman_html($edit_data['judul'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pasangan_mata_uang" class="form-label">Pasangan Mata Uang</label>
                        <select class="form-select" id="pasangan_mata_uang" name="pasangan_mata_uang" required>
                            <option value="">Pilih Pasangan</option>
                            <option value="XAUUSD" <?= ($edit_data['pasangan_mata_uang'] ?? '') === 'XAUUSD' ? 'selected' : '' ?>>XAUUSD</option>
                            <option value="EURUSD" <?= ($edit_data['pasangan_mata_uang'] ?? '') === 'EURUSD' ? 'selected' : '' ?>>EURUSD</option>
                            <option value="GBPUSD" <?= ($edit_data['pasangan_mata_uang'] ?? '') === 'GBPUSD' ? 'selected' : '' ?>>GBPUSD</option>
                            <option value="USDJPY" <?= ($edit_data['pasangan_mata_uang'] ?? '') === 'USDJPY' ? 'selected' : '' ?>>USDJPY</option>
                            <option value="AUDUSD" <?= ($edit_data['pasangan_mata_uang'] ?? '') === 'AUDUSD' ? 'selected' : '' ?>>AUDUSD</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" 
                               value="<?= $edit_data['tanggal'] ?? date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar (Opsional)</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                        <div class="preview-gambar mt-2"></div>
                        <div class="progress-upload mt-2" style="display: none;"></div>
                        <input type="hidden" name="gambar_url" value="<?= aman_html($edit_data['gambar'] ?? '') ?>">
                        <small class="text-muted">Maksimal 5MB, format JPG/PNG/GIF/WebP</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="isi_analisis" class="form-label">Isi Analisis</label>
                        <textarea class="form-control" id="isi_analisis" name="isi_analisis" rows="10" required><?= aman_html($edit_data['isi_analisis'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emas">
                        <i class="fa-solid fa-save me-1"></i><?= $edit_data ? 'Update' : 'Simpan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= aman_html(basis_url('aset/js/upload_gambar.js')) ?>"></script>
<script>
// Auto-show modal if editing
<?php if ($edit_data): ?>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('modalTambah'));
    modal.show();
});
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../includes/kaki.php'; ?>
