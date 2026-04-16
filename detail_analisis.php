<?php
declare(strict_types=1);
$judul_halaman = 'Detail Analisis';
$deskripsi_meta = 'Detail analisis pasar ARFXTRADE.';
require_once __DIR__ . '/includes/kepala.php';

$id_analisis = (int)($_GET['id'] ?? 0);
$analisis = null;

if ($id_analisis > 0) {
    try {
        $sql = "SELECT * FROM analisis WHERE id_analisis = ?";
        $stmt = jalankan_query_siap($sql, 'i', [$id_analisis]);
        $hasil = $stmt->get_result();
        $analisis = $hasil->fetch_assoc();
        $stmt->close();
    } catch (Exception $e) {
        $analisis = null;
    }
}

if (!$analisis) {
    header('Location: ' . basis_url(''));
    exit;
}

$judul_halaman = $analisis['judul'];
$deskripsi_meta = substr(strip_tags($analisis['isi_analisis']), 0, 160);
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="kartu-gelap p-5">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="fw-bold mb-2"><?= aman_html($analisis['judul']) ?></h1>
                            <div class="d-flex gap-3 text-secondary">
                                <span><i class="fa-solid fa-calendar me-1"></i><?= date('d M Y', strtotime($analisis['tanggal'])) ?></span>
                                <span><i class="fa-solid fa-chart-line me-1"></i><?= aman_html($analisis['pasangan_mata_uang']) ?></span>
                            </div>
                        </div>
                        <a href="<?= aman_html(basis_url('')) ?>" class="btn btn-outline-light">
                            <i class="fa-solid fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                    
                    <?php if (!empty($analisis['gambar'])): ?>
                        <div class="mb-4">
                            <img src="<?= aman_html($analisis['gambar']) ?>" class="img-fluid rounded" alt="<?= aman_html($analisis['judul']) ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="content">
                        <?= nl2br(aman_html($analisis['isi_analisis'])) ?>
                    </div>
                    
                    <div class="mt-5 pt-4 border-top border-secondary">
                        <h5 class="fw-semibold mb-3">Bagikan Analisis Ini</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(basis_url('detail_analisis.php?id=' . $analisis['id_analisis'])) ?>" 
                               target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fa-brands fa-facebook me-1"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(basis_url('detail_analisis.php?id=' . $analisis['id_analisis'])) ?>&text=<?= urlencode($analisis['judul']) ?>" 
                               target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="fa-brands fa-twitter me-1"></i>Twitter
                            </a>
                            <a href="https://wa.me/?text=<?= urlencode($analisis['judul'] . ' - ' . basis_url('detail_analisis.php?id=' . $analisis['id_analisis'])) ?>" 
                               target="_blank" class="btn btn-outline-success btn-sm">
                                <i class="fa-brands fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>








