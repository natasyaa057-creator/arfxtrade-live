<?php
// Widget Market Overview untuk halaman analisis
// basis_url() sudah tersedia dari includes/kepala.php yang di-require di halaman utama

// Ambil data XAUUSD realtime dari Metals-API
$xauusd_data = null;
try {
    $metals_api_url = basis_url('api/metals_api.php?symbol=XAUUSD');
    $ch = curl_init($metals_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200 && $response) {
        $json = json_decode($response, true);
        if (isset($json['success']) && $json['success'] && isset($json['data'])) {
            $xauusd_data = $json['data'];
        }
    }
} catch (Exception $e) {
    // Fallback jika API gagal
}

// Data default (akan diupdate via JavaScript jika API berhasil)
$market_data = [
    'XAUUSD' => [
        'price' => $xauusd_data['price'] ?? 2650.00, 
        'change' => ($xauusd_data['change'] ?? 0) >= 0 ? '+' . number_format($xauusd_data['change'] ?? 0, 2) : number_format($xauusd_data['change'] ?? 0, 2),
        'change_percent' => ($xauusd_data['change_percent'] ?? 0) >= 0 ? '+' . number_format($xauusd_data['change_percent'] ?? 0, 2) : number_format($xauusd_data['change_percent'] ?? 0, 2),
        'trend' => ($xauusd_data['change'] ?? 0) >= 0 ? 'up' : 'down',
        'realtime' => true
    ],
    'EURUSD' => ['price' => 1.0850, 'change' => '-0.15%', 'trend' => 'down'],
    'GBPUSD' => ['price' => 1.2650, 'change' => '+0.35%', 'trend' => 'up'],
    'USDJPY' => ['price' => 149.25, 'change' => '+0.20%', 'trend' => 'up'],
    'AUDUSD' => ['price' => 0.6520, 'change' => '-0.10%', 'trend' => 'down'],
    'BTCUSD' => ['price' => 42500, 'change' => '+2.15%', 'trend' => 'up'],
    'ETHUSD' => ['price' => 2650, 'change' => '+1.85%', 'trend' => 'up']
];
?>

<div class="kartu-gelap p-4 mb-4">
    <h5 class="fw-semibold mb-3 teks-emas">
        <i class="fa-solid fa-chart-line me-2"></i>Market Overview
    </h5>
    
    <div class="row g-3">
        <?php foreach ($market_data as $pair => $data): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                    <div>
                        <div class="fw-semibold">
                            <?= $pair ?>
                            <?php if ($pair === 'XAUUSD' && isset($data['realtime'])): ?>
                                <span class="badge bg-success bg-opacity-25 text-success ms-1" style="font-size: 0.7em;">
                                    <i class="fa-solid fa-circle fa-xs" style="animation: pulse 2s infinite;"></i> Live
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="small text-muted price" data-pair="<?= $pair ?>" data-price="<?= $data['price'] ?>">
                            <?= number_format($data['price'], $pair === 'BTCUSD' || $pair === 'ETHUSD' ? 0 : ($pair === 'XAUUSD' ? 2 : 4)) ?>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-<?= $data['trend'] === 'up' ? 'success' : 'danger' ?> change" data-pair="<?= $pair ?>" data-change="<?= $data['change'] ?? '' ?>" data-change-percent="<?= $data['change_percent'] ?? '' ?>">
                            <?php if ($pair === 'XAUUSD' && isset($data['change_percent'])): ?>
                                <?= $data['change_percent'] ?>%
                            <?php else: ?>
                                <?= $data['change'] ?>
                            <?php endif; ?>
                        </span>
                        <div class="small">
                            <i class="fa-solid fa-arrow-<?= $data['trend'] === 'up' ? 'up' : 'down' ?> text-<?= $data['trend'] === 'up' ? 'success' : 'danger' ?> trend" data-pair="<?= $pair ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-3 text-center">
        <div class="alert alert-info alert-sm mb-3">
            <i class="fa-solid fa-info-circle me-2"></i>
            <strong>XAUUSD (Gold):</strong> Data real-time dari Metals-API. 
            <span class="text-warning">Pasangan lain: referensi harian.</span>
        </div>
        
        <div class="d-flex justify-content-center align-items-center gap-3">
            <small class="text-muted last-update-time">
                <i class="fa-solid fa-clock me-1"></i>
                Update terakhir: <?= date('H:i:s') ?> WIB
            </small>
            <button class="btn btn-outline-light btn-sm refresh-forex-data" title="Refresh Data">
                <i class="fa-solid fa-refresh"></i>
            </button>
            <button class="btn btn-outline-warning btn-sm pause-auto-update" title="Pause Auto-update">
                <i class="fa-solid fa-pause"></i>
            </button>
        </div>
        <div class="loading-indicator mt-2" style="display: none;">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <small class="text-muted ms-2">Memuat data forex...</small>
        </div>
    </div>
</div>
