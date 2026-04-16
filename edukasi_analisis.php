<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/premium_guard.php';
premiumOnly(); // Proteksi premium - hanya member aktif yang bisa akses

$judul_halaman = 'Edukasi Analisis Trading - ARFXTRADE Premium';
$deskripsi_meta = 'Pelajari analisis fundamental dan teknikal untuk trading forex yang profesional. Modul lengkap dengan tools interaktif dan kalkulator eksklusif untuk member premium.';
require_once __DIR__ . '/includes/kepala.php';

// Data edukasi statis (tidak menggunakan database untuk menghindari error)
$materi_edukasi = [];

// Data untuk analisis fundamental
$fundamental_data = [
    'economic_indicators' => [
        'GDP' => [
            'nama' => 'Gross Domestic Product (GDP)',
            'deskripsi' => 'Total nilai barang dan jasa yang diproduksi dalam suatu negara',
            'dampak' => 'Tinggi',
            'frekuensi' => 'Triwulanan',
            'contoh' => 'GDP AS Q3 2024: +2.1%'
        ],
        'CPI' => [
            'nama' => 'Consumer Price Index (CPI)',
            'deskripsi' => 'Indeks harga konsumen untuk mengukur inflasi',
            'dampak' => 'Tinggi',
            'frekuensi' => 'Bulanan',
            'contoh' => 'CPI AS November 2024: 3.2%'
        ],
        'NFP' => [
            'nama' => 'Non-Farm Payrolls (NFP)',
            'deskripsi' => 'Jumlah pekerjaan yang ditambahkan di sektor non-pertanian',
            'dampak' => 'Sangat Tinggi',
            'frekuensi' => 'Bulanan',
            'contoh' => 'NFP AS November 2024: +150K'
        ],
        'Interest_Rate' => [
            'nama' => 'Suku Bunga Bank Sentral',
            'deskripsi' => 'Tingkat suku bunga yang ditetapkan bank sentral',
            'dampak' => 'Sangat Tinggi',
            'frekuensi' => '8x per tahun',
            'contoh' => 'Fed Rate: 5.25-5.50%'
        ]
    ],
    'central_banks' => [
        'FED' => [
            'nama' => 'Federal Reserve (FED)',
            'negara' => 'Amerika Serikat',
            'mata_uang' => 'USD',
            'suku_bunga' => '5.25-5.50%',
            'kepala' => 'Jerome Powell',
            'pertemuan' => '8x per tahun'
        ],
        'ECB' => [
            'nama' => 'European Central Bank (ECB)',
            'negara' => 'Zona Euro',
            'mata_uang' => 'EUR',
            'suku_bunga' => '4.25%',
            'kepala' => 'Christine Lagarde',
            'pertemuan' => '8x per tahun'
        ],
        'BOE' => [
            'nama' => 'Bank of England (BOE)',
            'negara' => 'Inggris',
            'mata_uang' => 'GBP',
            'suku_bunga' => '5.25%',
            'kepala' => 'Andrew Bailey',
            'pertemuan' => '8x per tahun'
        ],
        'BOJ' => [
            'nama' => 'Bank of Japan (BOJ)',
            'negara' => 'Jepang',
            'mata_uang' => 'JPY',
            'suku_bunga' => '-0.1%',
            'kepala' => 'Kazuo Ueda',
            'pertemuan' => '8x per tahun'
        ]
    ]
];

// Data untuk analisis teknikal
$teknikal_data = [
    'indikator_trend' => [
        'MA' => [
            'nama' => 'Moving Average (MA)',
            'deskripsi' => 'Rata-rata harga dalam periode tertentu',
            'jenis' => ['SMA', 'EMA', 'WMA'],
            'periode' => ['20', '50', '100', '200'],
            'sinyal' => 'Golden Cross (MA50 > MA200), Death Cross (MA50 < MA200)'
        ],
        'MACD' => [
            'nama' => 'MACD (Moving Average Convergence Divergence)',
            'deskripsi' => 'Indikator momentum yang menunjukkan hubungan antara dua MA',
            'komponen' => ['MACD Line', 'Signal Line', 'Histogram'],
            'sinyal' => 'Crossover MACD dan Signal Line'
        ],
        'RSI' => [
            'nama' => 'RSI (Relative Strength Index)',
            'deskripsi' => 'Indikator momentum untuk mengukur kecepatan perubahan harga',
            'range' => '0-100',
            'sinyal' => 'Overbought (>70), Oversold (<30)'
        ]
    ],
    'support_resistance' => [
        'support' => [
            'nama' => 'Support Level',
            'deskripsi' => 'Level harga dimana permintaan cukup kuat untuk mencegah penurunan lebih lanjut',
            'cara_identifikasi' => 'Titik terendah berulang, volume tinggi, psychological level'
        ],
        'resistance' => [
            'nama' => 'Resistance Level',
            'deskripsi' => 'Level harga dimana penawaran cukup kuat untuk mencegah kenaikan lebih lanjut',
            'cara_identifikasi' => 'Titik tertinggi berulang, volume tinggi, psychological level'
        ]
    ],
    'chart_patterns' => [
        'reversal' => [
            'Head and Shoulders' => 'Pola pembalikan bearish dengan 3 puncak',
            'Double Top' => 'Pola pembalikan bearish dengan 2 puncak sama tinggi',
            'Double Bottom' => 'Pola pembalikan bullish dengan 2 lembah sama rendah'
        ],
        'continuation' => [
            'Triangle' => 'Pola kelanjutan dengan konvergensi support dan resistance',
            'Flag' => 'Pola kelanjutan setelah pergerakan kuat',
            'Pennant' => 'Pola kelanjutan dengan konvergensi yang lebih tajam'
        ]
    ]
];

$journey_steps = [
    [
        'tahun' => '2014',
        'judul' => 'Bangkit dari Krisis',
        'deskripsi' => 'Memulai perjalanan trading untuk memperbaiki kondisi finansial keluarga.'
    ],
    [
        'tahun' => '2018',
        'judul' => 'Mengembangkan Sistem',
        'deskripsi' => 'Menyusun framework analisis berbasis data dan manajemen risiko ketat.'
    ],
    [
        'tahun' => '2021',
        'judul' => 'Komunitas Premium',
        'deskripsi' => 'Membangun komunitas trader untuk belajar, berbagi strategi, dan tumbuh bersama.'
    ],
    [
        'tahun' => '2024',
        'judul' => '100x Growth Mindset',
        'deskripsi' => 'Mencapai pertumbuhan portofolio berkelanjutan dengan transparansi penuh.'
    ]
];

$selling_points = [
    [
        'ikon' => 'fa-users',
        'judul' => 'Community Gathering',
        'deskripsi' => 'Diskusi harian, sesi live, dan analisis mingguan langsung dari praktisi aktif.'
    ],
    [
        'ikon' => 'fa-arrows-rotate',
        'judul' => 'Regular Market Update',
        'deskripsi' => 'Update kondisi market real-time agar selalu selangkah di depan.'
    ],
    [
        'ikon' => 'fa-check-double',
        'judul' => 'Portofolio Transparan',
        'deskripsi' => 'Perjalanan trading yang terdokumentasi dan bisa diikuti setiap member.'
    ]
];

$plans = [
    [
        'nama' => 'Akses VIP',
        'harga' => '8.990.000',
        'status' => 'Sold Out',
        'deskripsi' => [
            'Akses kelas tahunan & komunitas',
            'Update setup dan watchlist mingguan',
            'Sesi Q&A eksklusif'
        ]
    ],
    [
        'nama' => 'Akses VVIP',
        'harga' => '22.990.000',
        'status' => 'Sold Out',
        'deskripsi' => [
            'Semua manfaat VIP',
            'Mentoring kecil & review portofolio',
            'Prioritas konsultasi strategi'
        ]
    ]
];

$faq_items = [
    [
        'pertanyaan' => 'Apakah komunitas ini aktif?',
        'jawaban' => 'Ya. Ada diskusi harian, live session terjadwal, serta analisis mingguan bersama mentor.'
    ],
    [
        'pertanyaan' => 'Apakah cocok untuk pemula?',
        'jawaban' => 'Materi disusun bertahap dari dasar hingga lanjutan, lengkap dengan contoh dan latihan.'
    ],
    [
        'pertanyaan' => 'Apakah wajib menggunakan exchange tertentu?',
        'jawaban' => 'Tidak. Kami memberi rekomendasi, tetapi Anda bebas menggunakan platform pilihan sendiri.'
    ],
    [
        'pertanyaan' => 'Apakah program ini langganan tahunan?',
        'jawaban' => 'Betul. Satu kali pembayaran memberikan akses penuh selama 12 bulan.'
    ],
    [
        'pertanyaan' => 'Bagaimana alur setelah pembayaran?',
        'jawaban' => 'Tim admin menghubungi via WhatsApp dalam 1x24 jam kerja untuk aktivasi akses.'
    ]
];

$payment_methods = [
    'Credit Card (Xendit)',
    'Bank Transfer BNI',
    'Bank Transfer BRI',
    'Bank Transfer Mandiri',
    'Bank Transfer Permata',
    'Bank Transfer CIMB',
    'Bank Transfer BCA',
    'DANA',
    'LinkAja',
    'QRIS',
    'OVO',
    'ShopeePay'
];
?>

<section class="pt-6 pb-5 mt-5 hero-analisis position-relative overflow-hidden">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7" data-aos="fade-right">
                <span class="badge bg-emas text-dark mb-3">Premium Member Only</span>
                <h1 class="display-4 fw-bold mb-4">Bangun <span class="teks-emas">Mindset 100x</span> dengan Analisis yang Terukur</h1>
                <p class="lead text-secondary mb-4">Ikuti perjalanan transparan tim ARFXTRADE yang berhasil mengubah modal terbatas menjadi portofolio delapan digit melalui riset mendalam, komunitas aktif, dan disiplin manajemen risiko.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#harga" class="btn btn-emas btn-lg px-4">
                        <i class="fa-solid fa-rocket me-2"></i>Gabung Sekarang
                    </a>
                    <a href="#kurikulum" class="btn btn-outline-light btn-lg px-4">
                        <i class="fa-solid fa-layer-group me-2"></i>Lihat Struktur Program
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-5">
                    <div>
                        <div class="h1 fw-bold mb-0 teks-emas">10+ Tahun</div>
                        <small class="text-secondary text-uppercase">Pengalaman teknologi & keuangan</small>
                    </div>
                    <div>
                        <div class="h1 fw-bold mb-0 teks-emas">100x</div>
                        <small class="text-secondary text-uppercase">Pertumbuhan portofolio tercatat</small>
                    </div>
                    <div>
                        <div class="h1 fw-bold mb-0 teks-emas">0 Penipuan</div>
                        <small class="text-secondary text-uppercase">Porto dipublikasikan real-time</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="kartu-gelap p-4 rounded-4 shadow-lg position-relative">
                    <div class="mb-4">
                        <small class="text-uppercase text-muted">Perjalanan Portofolio</small>
                        <h3 class="fw-semibold mt-2">30 Juta → 10 Miliar</h3>
                        <p class="text-secondary mb-0">Framework analisis + money management yang tervalidasi.</p>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-3">
                            <span class="icon-circle bg-success bg-opacity-10 text-success me-3"><i class="fa-solid fa-bolt"></i></span>
                            Konsistensi eksekusi ditunjang dashboard monitoring internal.
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <span class="icon-circle bg-info bg-opacity-10 text-info me-3"><i class="fa-solid fa-shield"></i></span>
                            Risiko selalu dikunci melalui position sizing adaptif.
                        </li>
                        <li class="d-flex align-items-center">
                            <span class="icon-circle bg-warning bg-opacity-10 text-warning me-3"><i class="fa-solid fa-chart-line"></i></span>
                            Setiap setup direkap & dievaluasi secara terbuka dalam komunitas.
                        </li>
                    </ul>
                    <div class="alert alert-warning mb-0">
                        <strong>Disclaimer:</strong> Trading futures berisiko tinggi. Hanya gunakan dana dingin; hasil historis tidak menjamin performa mendatang.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-gradient-dark text-light">
    <div class="container">
        <div class="text-center mb-5">
            <p class="text-uppercase text-secondary mb-2">Perjalanan</p>
            <h2 class="fw-bold">Road to Sustainable Profit</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($journey_steps as $step): ?>
            <div class="col-lg-3 col-md-6">
                <div class="kartu-gelap p-4 h-100 border-0 shadow-sm">
                    <span class="badge bg-emas text-dark mb-3"><?= aman_html($step['tahun']) ?></span>
                    <h5 class="fw-semibold mb-2"><?= aman_html($step['judul']) ?></h5>
                    <p class="text-secondary small mb-0"><?= aman_html($step['deskripsi']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-body-tertiary" id="keunggulan">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <p class="text-uppercase text-muted mb-2">Kenapa ARFXTRADE Premium?</p>
                <h2 class="fw-bold mb-3">Komunitas, Update, & Porto Transparan</h2>
                <p class="text-secondary">Kami fokus pada pembelajaran yang realistis dengan data real market, bukan mimpi instan. Member mendapat akses ke forum privat, sesi live, serta ringkasan strategi yang bisa langsung diterapkan.</p>
                <a href="#harga" class="btn btn-outline-emas mt-3">Lihat Paket Akses</a>
            </div>
            <div class="col-lg-7">
                <div class="row g-4">
                    <?php foreach ($selling_points as $point): ?>
                    <div class="col-md-6">
                        <div class="kartu-gelap p-4 h-100 rounded-4">
                            <div class="icon-circle bg-emas text-dark mb-3">
                                <i class="fa-solid <?= aman_html($point['ikon']) ?>"></i>
                            </div>
                            <h5 class="fw-semibold mb-2"><?= aman_html($point['judul']) ?></h5>
                            <p class="text-secondary small mb-0"><?= aman_html($point['deskripsi']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-dark text-light">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <p class="text-uppercase text-secondary mb-2">Mentor & Story</p>
                <h2 class="fw-bold mb-3">Kevin Jonathan Style, ARFXTRADE Execution</h2>
                <p class="text-secondary mb-4">Berawal dari kondisi finansial keluarga yang jatuh, perjalanan kami membuktikan bahwa strategi berbasis data + disiplin adalah kombinasi paling aman. Semua catatan transaksi dan reasoning disusun rapi sebagai bahan belajar.</p>
                <div class="d-flex gap-4 flex-wrap">
                    <div>
                        <h3 class="fw-bold teks-emas mb-0">2014</h3>
                        <small class="text-secondary">Mulai trading full-time</small>
                    </div>
                    <div>
                        <h3 class="fw-bold teks-emas mb-0">12M+</h3>
                        <small class="text-secondary">Nilai porto tersupervisi</small>
                    </div>
                    <div>
                        <h3 class="fw-bold teks-emas mb-0">>500</h3>
                        <small class="text-secondary">Member aktif komunitas</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kartu-gelap p-4 rounded-4">
                    <div class="mb-4">
                        <h5 class="fw-semibold">Prinsip Utama</h5>
                        <p class="text-secondary small mb-0">Logis, transparan, dan realistis. Keputusan beli/jual adalah tanggung jawab pribadi sehingga setiap member diberi kerangka berpikir, bukan sinyal instan.</p>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="alert alert-secondary bg-opacity-10 border-0 text-light">
                            <strong>Komitmen Transparansi:</strong> setiap screenshot porto, jurnal trading, hingga drawdown dirilis ke komunitas.
                        </div>
                        <div class="alert alert-warning bg-opacity-10 border-0 text-warning">
                            <strong>Risk Reminder:</strong> Crypto futures sangat fluktuatif. Tidak ada garansi profit dan tidak ada refund.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" id="kurikulum">
    <div class="container">
        <div class="text-center mb-5">
            <p class="text-uppercase text-muted mb-2">Struktur Program</p>
            <h2 class="fw-bold">Fundamental + Teknikal, Praktis & Bisa Dieksekusi</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="kartu-gelap p-4 h-100 rounded-4">
                    <h4 class="fw-semibold mb-4 teks-emas">
                        <i class="fa-solid fa-chart-line me-2"></i>Analisis Fundamental
                    </h4>
                    <?php foreach ($fundamental_data['economic_indicators'] as $indicator): ?>
                    <div class="mb-4 p-3 border rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-semibold mb-0"><?= aman_html($indicator['nama']) ?></h6>
                            <span class="badge bg-<?= $indicator['dampak'] === 'Sangat Tinggi' ? 'danger' : ($indicator['dampak'] === 'Tinggi' ? 'warning' : 'success') ?>">
                                <?= aman_html($indicator['dampak']) ?>
                            </span>
                        </div>
                        <p class="text-secondary small mb-2"><?= aman_html($indicator['deskripsi']) ?></p>
                        <div class="d-flex justify-content-between text-small">
                            <span><small class="text-muted">Frekuensi:</small> <strong><?= aman_html($indicator['frekuensi']) ?></strong></span>
                            <span><small class="text-muted">Contoh:</small> <strong class="teks-emas"><?= aman_html($indicator['contoh']) ?></strong></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="kartu-gelap bg-opacity-50 p-3 rounded mb-3">
                        <h6 class="fw-semibold mb-3">Update Bank Sentral</h6>
                        <div class="row g-3">
                            <?php foreach ($fundamental_data['central_banks'] as $bank): ?>
                            <div class="col-6">
                                <div class="border rounded p-2 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="small"><?= aman_html($bank['nama']) ?></strong>
                                        <span class="badge bg-emas text-dark"><?= aman_html($bank['mata_uang']) ?></span>
                                    </div>
                                    <small class="text-muted d-block">Rate: <span class="teks-emas"><?= aman_html($bank['suku_bunga']) ?></span></small>
                                    <small class="text-muted d-block">Kepala: <?= aman_html($bank['kepala']) ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="alert alert-dark mb-0">
                        <strong>Panduan Praktis:</strong> identifikasi event → bandingkan data → tentukan bias makro sebelum masuk posisi.
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kartu-gelap p-4 h-100 rounded-4">
                    <h4 class="fw-semibold mb-4 teks-emas">
                        <i class="fa-solid fa-chart-area me-2"></i>Analisis Teknikal
                    </h4>
                    <?php foreach ($teknikal_data['indikator_trend'] as $indicator): ?>
                    <div class="mb-4 p-3 border rounded">
                        <h6 class="fw-semibold mb-2"><?= aman_html($indicator['nama']) ?></h6>
                        <p class="text-secondary small mb-3"><?= aman_html($indicator['deskripsi']) ?></p>
                        <?php if (isset($indicator['jenis'])): ?>
                        <div class="mb-2">
                            <small class="text-muted">Jenis:</small>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <?php foreach ($indicator['jenis'] as $jenis): ?>
                                <span class="badge bg-secondary"><?= aman_html($jenis) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($indicator['periode'])): ?>
                        <div class="mb-2">
                            <small class="text-muted">Periode:</small>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <?php foreach ($indicator['periode'] as $periode): ?>
                                <span class="badge bg-info"><?= aman_html($periode) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="mt-3">
                            <small class="text-muted">Sinyal Utama:</small>
                            <div class="fw-semibold teks-emas small"><?= aman_html($indicator['sinyal']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="row g-3 mb-4">
                        <?php foreach ($teknikal_data['support_resistance'] as $level): ?>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h6 class="fw-semibold mb-1"><?= aman_html($level['nama']) ?></h6>
                                <p class="text-secondary small mb-2"><?= aman_html($level['deskripsi']) ?></p>
                                <small class="text-muted">Identifikasi:</small>
                                <div class="fw-semibold teks-emas small"><?= aman_html($level['cara_identifikasi']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="kartu-gelap bg-opacity-50 p-3 rounded">
                        <h6 class="fw-semibold mb-2">Chart Patterns</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong class="text-success small">Reversal</strong>
                                <ul class="small text-secondary mt-2 mb-0">
                                    <?php foreach ($teknikal_data['chart_patterns']['reversal'] as $pattern => $desc): ?>
                                    <li><strong><?= aman_html($pattern) ?>:</strong> <?= aman_html($desc) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <strong class="text-info small">Continuation</strong>
                                <ul class="small text-secondary mt-2 mb-0">
                                    <?php foreach ($teknikal_data['chart_patterns']['continuation'] as $pattern => $desc): ?>
                                    <li><strong><?= aman_html($pattern) ?>:</strong> <?= aman_html($desc) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-body-tertiary" id="tools">
    <div class="container">
        <div class="text-center mb-5">
            <p class="text-uppercase text-muted mb-2">Tools & Kalkulator</p>
            <h2 class="fw-bold">Eksekusi Lebih Presisi</h2>
            <p class="text-secondary">Gunakan kalkulator internal untuk menghitung position size, risk reward, pivot, dan level Fibonacci secara instan.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="kartu-gelap p-4 rounded-4 h-100">
                    <h4 class="fw-semibold mb-4 teks-emas">
                        <i class="fa-solid fa-calculator me-2"></i>Position Size
                    </h4>
                    <form id="positionSizeForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Account Balance ($)</label>
                                <input type="number" class="form-control" id="accountBalance" value="10000" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Risk (%)</label>
                                <input type="number" class="form-control" id="riskPercentage" value="2" step="0.1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Entry Price</label>
                                <input type="number" class="form-control" id="entryPrice" value="1.0850" step="0.0001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stop Loss</label>
                                <input type="number" class="form-control" id="stopLoss" value="1.0800" step="0.0001">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-emas w-100" onclick="calculatePositionSize()">
                                    <i class="fa-solid fa-play me-2"></i>Hitung Position Size
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="positionSizeResult" class="mt-4" style="display: none;">
                        <div class="alert alert-success mb-0">
                            <h6 class="fw-semibold mb-2">Hasil Perhitungan:</h6>
                            <div id="positionSizeDetails"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kartu-gelap p-4 rounded-4 h-100">
                    <h4 class="fw-semibold mb-4 teks-emas">
                        <i class="fa-solid fa-chart-pie me-2"></i>Risk Reward
                    </h4>
                    <form id="riskRewardForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Entry Price</label>
                                <input type="number" class="form-control" id="rrEntryPrice" value="1.0850" step="0.0001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stop Loss</label>
                                <input type="number" class="form-control" id="rrStopLoss" value="1.0800" step="0.0001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Take Profit</label>
                                <input type="number" class="form-control" id="takeProfit" value="1.0950" step="0.0001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Position Size (Lots)</label>
                                <input type="number" class="form-control" id="positionSize" value="1" step="0.01">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-emas w-100" onclick="calculateRiskReward()">
                                    <i class="fa-solid fa-chart-pie me-2"></i>Hitung Risk Reward
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="riskRewardResult" class="mt-4" style="display: none;">
                        <div class="alert alert-info mb-0">
                            <h6 class="fw-semibold mb-2">Hasil Perhitungan:</h6>
                            <div id="riskRewardDetails"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kartu-gelap p-4 rounded-4 h-100">
                    <h4 class="fw-semibold mb-4 teks-emas">
                        <i class="fa-solid fa-crosshairs me-2"></i>Pivot Point
                    </h4>
                    <form id="pivotPointForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">High</label>
                                <input type="number" class="form-control" id="pivotHigh" value="1.0900" step="0.0001">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Low</label>
                                <input type="number" class="form-control" id="pivotLow" value="1.0800" step="0.0001">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Close</label>
                                <input type="number" class="form-control" id="pivotClose" value="1.0850" step="0.0001">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-emas w-100" onclick="calculatePivotPoints()">
                                    <i class="fa-solid fa-crosshairs me-2"></i>Hitung Pivot Points
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="pivotPointResult" class="mt-4" style="display: none;">
                        <div class="alert alert-warning mb-0">
                            <h6 class="fw-semibold mb-2">Pivot Points:</h6>
                            <div id="pivotPointDetails"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kartu-gelap p-4 rounded-4 h-100">
                    <h4 class="fw-semibold mb-4 teks-emas">
                        <i class="fa-solid fa-wave-square me-2"></i>Fibonacci Level
                    </h4>
                    <form id="fibonacciForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">High Price</label>
                                <input type="number" class="form-control" id="fibHigh" value="1.0900" step="0.0001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Low Price</label>
                                <input type="number" class="form-control" id="fibLow" value="1.0800" step="0.0001">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-emas w-100" onclick="calculateFibonacci()">
                                    <i class="fa-solid fa-wave-square me-2"></i>Hitung Fibonacci
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="fibonacciResult" class="mt-4" style="display: none;">
                        <div class="alert alert-primary mb-0">
                            <h6 class="fw-semibold mb-2">Fibonacci Levels:</h6>
                            <div id="fibonacciDetails"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-dark text-light" id="harga">
    <div class="container">
        <div class="text-center mb-5">
            <p class="text-uppercase text-secondary mb-2">Pilih Paket Akses</p>
            <h2 class="fw-bold">Program Tahunan untuk 12 Bulan Penuh</h2>
            <p class="text-secondary">Harga sudah termasuk akses komunitas, materi on-demand, live session, dan seluruh tools premium.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($plans as $plan): ?>
            <div class="col-lg-6">
                <div class="kartu-gelap p-4 rounded-4 h-100 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-semibold mb-0"><?= aman_html($plan['nama']) ?></h4>
                        <span class="badge bg-danger text-uppercase"><?= aman_html($plan['status']) ?></span>
                    </div>
                    <div class="display-5 fw-bold teks-emas mb-3">Rp<?= aman_html($plan['harga']) ?></div>
                    <ul class="list-unstyled mb-4">
                        <?php foreach ($plan['deskripsi'] as $item): ?>
                        <li class="d-flex align-items-start mb-2">
                            <i class="fa-solid fa-check text-success me-2 mt-1"></i>
                            <span><?= aman_html($item) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <button class="btn btn-outline-light w-100 py-2" disabled>Sold Out</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-5">
            <h5 class="fw-semibold mb-3">Metode Pembayaran melalui Xendit</h5>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($payment_methods as $method): ?>
                <span class="badge bg-secondary bg-opacity-25 text-light py-2 px-3"><?= aman_html($method) ?></span>
                <?php endforeach; ?>
            </div>
            <p class="text-secondary small mt-3 mb-0">Gunakan Bank Permata untuk transfer dari bank lain dan pilih metode real-time online agar verifikasi otomatis. Semua pembayaran bersifat final & non-refundable.</p>
        </div>
    </div>
</section>

<section class="py-5 bg-body-tertiary" id="faq">
    <div class="container">
        <div class="text-center mb-5">
            <p class="text-uppercase text-muted mb-2">FAQ</p>
            <h2 class="fw-bold">Pertanyaan yang Sering Ditanyakan</h2>
        </div>
        <div class="accordion" id="accordionFAQ">
            <?php foreach ($faq_items as $index => $faq): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading<?= $index ?>">
                    <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?= $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="faqCollapse<?= $index ?>">
                        <?= aman_html($faq['pertanyaan']) ?>
                    </button>
                </h2>
                <div id="faqCollapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="faqHeading<?= $index ?>" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body text-secondary">
                        <?= aman_html($faq['jawaban']) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-dark text-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3">Siap Naik Level?</h2>
                <p class="text-secondary mb-4">Setelah checkout, invoice dan kontak admin akan dikirim via email. Admin akan menghubungi melalui WhatsApp maksimal 1x24 jam kerja. Jika belum ada respon, hubungi <a href="mailto:teamkjoacademy@gmail.com" class="teks-emas">teamkjoacademy@gmail.com</a>.</p>
                <ul class="list-unstyled text-secondary small mb-0">
                    <li><strong>Langkah 1:</strong> Lengkapi formulir pemesanan.</li>
                    <li><strong>Langkah 2:</strong> Tunggu verifikasi admin.</li>
                    <li><strong>Langkah 3:</strong> Gabung komunitas & mulai belajar.</li>
                </ul>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="#harga" class="btn btn-emas btn-lg w-100 w-lg-auto">Daftar Sekarang</a>
                <p class="text-secondary small mt-3 mb-0">Dengan membeli paket, Anda menyetujui S&K dan kebijakan privasi ARFXTRADE.</p>
            </div>
        </div>
    </div>
</section>

<script src="<?= aman_html(basis_url('aset/js/edukasi_analisis.js')) ?>"></script>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>



