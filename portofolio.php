<?php
declare(strict_types=1);
$judul_halaman = 'Portofolio Trading';
$deskripsi_meta = 'Portofolio trading ARFXTRADE - hasil trading konsisten, strategi yang digunakan, dan contoh analisis pasar forex dan komoditas.';
require_once __DIR__ . '/includes/kepala.php';

// Ambil data portofolio dari database
$portofolio_data = [];
try {
    $sql = "SELECT * FROM portofolio ORDER BY tanggal_upload DESC";
    $stmt = jalankan_query_siap($sql, '', []);
    $hasil = $stmt->get_result();
    $portofolio_data = $hasil->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    // Jika tabel belum ada, gunakan data dummy
    $portofolio_data = [
        [
            'id_portofolio' => 1,
            'judul' => 'Analisis XAUUSD - Breakout Pattern',
            'kategori' => 'XAUUSD',
            'deskripsi' => 'Analisis breakout pattern pada XAUUSD dengan target 2:1 risk-reward ratio.',
            'gambar' => 'aset/gambar/portofolio-1.jpg',
            'tanggal_upload' => '2024-01-15'
        ],
        [
            'id_portofolio' => 2,
            'judul' => 'EURUSD Swing Trade - Support Bounce',
            'kategori' => 'EURUSD',
            'deskripsi' => 'Swing trade EURUSD dengan bounce dari level support kuat.',
            'gambar' => 'aset/gambar/portofolio-2.jpg',
            'tanggal_upload' => '2024-01-10'
        ],
        [
            'id_portofolio' => 3,
            'judul' => 'GBPUSD Scalping Strategy',
            'kategori' => 'GBPUSD',
            'deskripsi' => 'Strategi scalping GBPUSD dengan timeframe 5 menit.',
            'gambar' => 'aset/gambar/portofolio-3.jpg',
            'tanggal_upload' => '2024-01-05'
        ]
    ];
}

// Kategori unik untuk filter
$kategori_unik = array_unique(array_column($portofolio_data, 'kategori'));
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h1 class="display-5 fw-bold mb-3">Portofolio <span class="teks-emas">Trading</span></h1>
            <p class="lead text-secondary">Hasil trading konsisten dan strategi yang terbukti</p>
        </div>
        
        <!-- A. Performance Summary -->
        <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="col-12">
                <?php include 'widgets/performance_summary.php'; ?>
            </div>
        </div>
        
        <!-- B. Trading Journal / Riwayat Transaksi -->
        <div class="row mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="col-12">
                <?php include 'widgets/trading_journal.php'; ?>
            </div>
        </div>
        
        <!-- C. Grafik Kinerja -->
        <div class="row mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="col-12 mb-4">
                <h5 class="fw-semibold mb-3 teks-emas">
                    <i class="fa-solid fa-chart-area me-2"></i>Grafik Kinerja
                </h5>
            </div>
            
            <!-- Growth Performance -->
            <div class="col-12 mb-4">
                <div class="kartu-gelap p-4">
                    <h6 class="fw-semibold mb-3">Growth Performance</h6>
                    <div style="position: relative; height: 300px;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Histogram Profit/Loss -->
            <div class="col-12">
                <div class="kartu-gelap p-4">
                    <h6 class="fw-semibold mb-3">Histogram Profit/Loss</h6>
                    <div style="position: relative; height: 300px;">
                        <canvas id="histogramChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filter Kategori -->
        <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="col-12">
                <div class="kartu-gelap p-4">
                    <h5 class="fw-semibold mb-3">Filter Kategori</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-light btn-sm filter-btn active" data-kategori="semua">
                            Semua
                        </button>
                        <?php foreach ($kategori_unik as $kategori): ?>
                            <button class="btn btn-outline-light btn-sm filter-btn" data-kategori="<?= aman_html($kategori) ?>">
                                <?= aman_html($kategori) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grid Portofolio -->
        <div class="row g-4" id="portofolioGrid">
            <?php foreach ($portofolio_data as $item): ?>
                <div class="col-lg-4 col-md-6 portofolio-item" data-kategori="<?= aman_html($item['kategori']) ?>" data-aos="fade-up">
                    <div class="kartu-gelap h-100">
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <img src="<?= aman_html($item['gambar'] ?? 'https://placehold.co/400x200/0a0a0a/ffffff?text=' . urlencode($item['judul'])) ?>" 
                                 class="img-fluid w-100 h-100" 
                                 style="object-fit: cover;" 
                                 alt="<?= aman_html($item['judul']) ?>"
                                 onerror="this.src='https://placehold.co/400x200/0a0a0a/ffffff?text=Portofolio';">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-emas text-dark"><?= aman_html($item['kategori']) ?></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h5 class="fw-semibold mb-2"><?= aman_html($item['judul']) ?></h5>
                            <p class="text-secondary small mb-3"><?= aman_html($item['deskripsi']) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fa-solid fa-calendar me-1"></i>
                                    <?= date('d M Y', strtotime($item['tanggal_upload'])) ?>
                                </small>
                                <button class="btn btn-emas btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#modalDetail" 
                                        data-judul="<?= aman_html($item['judul']) ?>"
                                        data-kategori="<?= aman_html($item['kategori']) ?>"
                                        data-deskripsi="<?= aman_html($item['deskripsi']) ?>"
                                        data-tanggal="<?= date('d M Y', strtotime($item['tanggal_upload'])) ?>">
                                    Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($portofolio_data)): ?>
            <div class="text-center py-5" data-aos="fade-up">
                <div class="kartu-gelap p-5">
                    <i class="fa-solid fa-chart-line teks-emas fa-3x mb-3"></i>
                    <h4 class="fw-semibold mb-2">Portofolio Belum Tersedia</h4>
                    <p class="text-secondary">Portofolio trading akan segera ditambahkan.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Detail Portofolio -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content kartu-gelap">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" id="modalDetailLabel">Detail Portofolio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="modalGambar" src="" class="img-fluid rounded" alt="">
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-semibold teks-emas">Kategori</h6>
                        <p id="modalKategori" class="mb-3"></p>
                        
                        <h6 class="fw-semibold teks-emas">Tanggal</h6>
                        <p id="modalTanggal" class="mb-3"></p>
                        
                        <h6 class="fw-semibold teks-emas">Deskripsi</h6>
                        <p id="modalDeskripsi"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js untuk grafik kinerja -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= aman_html(basis_url('aset/js/portfolio_charts.js')) ?>"></script>

<script>
// Filter portofolio berdasarkan kategori
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portofolioItems = document.querySelectorAll('.portofolio-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const kategori = this.getAttribute('data-kategori');
            
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            portofolioItems.forEach(item => {
                if (kategori === 'semua' || item.getAttribute('data-kategori') === kategori) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Modal detail
    const modalDetail = document.getElementById('modalDetail');
    modalDetail.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const judul = button.getAttribute('data-judul');
        const kategori = button.getAttribute('data-kategori');
        const deskripsi = button.getAttribute('data-deskripsi');
        const tanggal = button.getAttribute('data-tanggal');
        
        document.getElementById('modalDetailLabel').textContent = judul;
        document.getElementById('modalKategori').textContent = kategori;
        document.getElementById('modalTanggal').textContent = tanggal;
        document.getElementById('modalDeskripsi').textContent = deskripsi;
        document.getElementById('modalGambar').src = button.closest('.kartu-gelap').querySelector('img').src;
    });
});
</script>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>








