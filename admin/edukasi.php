<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/admin_auth.php';

$judul_halaman = 'Kelola Edukasi';
$deskripsi_meta = 'Kelola materi edukasi trading ARFXTRADE.';
require_once __DIR__ . '/../includes/kepala.php';

$pesan_sukses = '';
$pesan_error = '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id_materi = (int)($_POST['id_materi'] ?? 0);
    $token_csrf = $_POST['token_csrf'] ?? '';
    
    if (!verifikasi_csrf($token_csrf)) {
        $pesan_error = 'Token keamanan tidak valid.';
    } elseif ($aksi === 'tambah' || $aksi === 'edit') {
        $judul = trim($_POST['judul'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $isi_materi = trim($_POST['isi_materi'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        
        if (empty($judul) || empty($kategori) || empty($isi_materi)) {
            $pesan_error = 'Judul, kategori, dan isi materi harus diisi.';
        } else {
            try {
                if ($aksi === 'tambah') {
                    $sql = "INSERT INTO edukasi (judul, kategori, isi_materi, video_url) VALUES (?, ?, ?, ?)";
                    $stmt = jalankan_query_siap($sql, 'ssss', [$judul, $kategori, $isi_materi, $video_url]);
                    $pesan_sukses = 'Materi edukasi berhasil ditambahkan.';
                } else {
                    $sql = "UPDATE edukasi SET judul = ?, kategori = ?, isi_materi = ?, video_url = ? WHERE id_materi = ?";
                    $stmt = jalankan_query_siap($sql, 'ssssi', [$judul, $kategori, $isi_materi, $video_url, $id_materi]);
                    $pesan_sukses = 'Materi edukasi berhasil diperbarui.';
                }
                $stmt->close();
            } catch (Exception $e) {
                $pesan_error = 'Gagal menyimpan materi edukasi.';
            }
        }
    } elseif ($aksi === 'hapus' && $id_materi > 0) {
        try {
            $sql = "DELETE FROM edukasi WHERE id_materi = ?";
            $stmt = jalankan_query_siap($sql, 'i', [$id_materi]);
            $stmt->close();
            $pesan_sukses = 'Materi edukasi berhasil dihapus.';
        } catch (Exception $e) {
            $pesan_error = 'Gagal menghapus materi edukasi.';
        }
    }
}

// Ambil data edukasi
$edukasi_data = [];
try {
    $sql = "SELECT * FROM edukasi ORDER BY id_materi DESC";
    $stmt = jalankan_query_siap($sql, '', []);
    $hasil = $stmt->get_result();
    $edukasi_data = $hasil->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $edukasi_data = [];
}

// Data untuk form edit
$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    foreach ($edukasi_data as $item) {
        if ($item['id_materi'] == $id_edit) {
            $edit_data = $item;
            break;
        }
    }
}
?>

<div class="container mt-5 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Kelola <span class="teks-emas">Edukasi</span></h1>
        <div class="d-flex gap-2">
            <button class="btn btn-emas" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus me-1"></i>Tambah Materi
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
                        <th>Kategori</th>
                        <th>Video</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($edukasi_data as $edukasi): ?>
                        <tr>
                            <td><?= $edukasi['id_materi'] ?></td>
                            <td><?= aman_html(substr($edukasi['judul'], 0, 50)) ?>...</td>
                            <td><span class="badge bg-emas text-dark"><?= aman_html($edukasi['kategori']) ?></span></td>
                            <td>
                                <?php if (!empty($edukasi['video_url'])): ?>
                                    <i class="fa-brands fa-youtube text-danger"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-book text-secondary"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="?edit=<?= $edukasi['id_materi'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="id_materi" value="<?= $edukasi['id_materi'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Hapus materi edukasi ini?')">
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
        
        <?php if (empty($edukasi_data)): ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-graduation-cap teks-emas fa-3x mb-3"></i>
                <h5 class="fw-semibold mb-2">Belum Ada Materi Edukasi</h5>
                <p class="text-secondary">Klik tombol "Tambah Materi" untuk membuat materi pertama.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah/Edit Edukasi -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content kartu-gelap">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" id="modalTambahLabel">
                    <?= $edit_data ? 'Edit Materi Edukasi' : 'Tambah Materi Edukasi Baru' ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="token_csrf" value="<?= aman_html(token_csrf()) ?>">
                    <input type="hidden" name="aksi" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_materi" value="<?= $edit_data['id_materi'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Materi</label>
                        <input type="text" class="form-control" id="judul" name="judul" 
                               value="<?= aman_html($edit_data['judul'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Dasar Trading" <?= ($edit_data['kategori'] ?? '') === 'Dasar Trading' ? 'selected' : '' ?>>Dasar Trading</option>
                            <option value="Money Management" <?= ($edit_data['kategori'] ?? '') === 'Money Management' ? 'selected' : '' ?>>Money Management</option>
                            <option value="Psikologi Trading" <?= ($edit_data['kategori'] ?? '') === 'Psikologi Trading' ? 'selected' : '' ?>>Psikologi Trading</option>
                            <option value="Analisis Teknikal" <?= ($edit_data['kategori'] ?? '') === 'Analisis Teknikal' ? 'selected' : '' ?>>Analisis Teknikal</option>
                            <option value="Strategi Trading" <?= ($edit_data['kategori'] ?? '') === 'Strategi Trading' ? 'selected' : '' ?>>Strategi Trading</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="video_url" class="form-label">URL Video YouTube (Opsional)</label>
                        <input type="url" class="form-control" id="video_url" name="video_url" 
                               value="<?= aman_html($edit_data['video_url'] ?? '') ?>" 
                               placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    
                    <div class="mb-3">
                        <label for="isi_materi" class="form-label">Isi Materi</label>
                        <textarea class="form-control" id="isi_materi" name="isi_materi" rows="10" required><?= aman_html($edit_data['isi_materi'] ?? '') ?></textarea>
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








