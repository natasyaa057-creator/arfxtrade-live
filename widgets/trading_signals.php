<?php
// Widget Trading Signals untuk halaman analisis
$trading_signals = [
    [
        'pair' => 'XAUUSD',
        'signal' => 'BUY',
        'entry' => '2020.00',
        'target' => '2035.00',
        'stop' => '2010.00',
        'confidence' => 85,
        'timeframe' => 'H4'
    ],
    [
        'pair' => 'EURUSD',
        'signal' => 'SELL',
        'entry' => '1.0870',
        'target' => '1.0820',
        'stop' => '1.0920',
        'confidence' => 78,
        'timeframe' => 'H1'
    ],
    [
        'pair' => 'GBPUSD',
        'signal' => 'BUY',
        'entry' => '1.2640',
        'target' => '1.2700',
        'stop' => '1.2580',
        'confidence' => 72,
        'timeframe' => 'H4'
    ],
    [
        'pair' => 'USDJPY',
        'signal' => 'HOLD',
        'entry' => '149.20',
        'target' => '150.00',
        'stop' => '148.50',
        'confidence' => 65,
        'timeframe' => 'D1'
    ]
];

function getSignalColor($signal) {
    switch($signal) {
        case 'BUY': return 'success';
        case 'SELL': return 'danger';
        case 'HOLD': return 'warning';
        default: return 'secondary';
    }
}

function getConfidenceColor($confidence) {
    if ($confidence >= 80) return 'success';
    if ($confidence >= 60) return 'warning';
    return 'danger';
}
?>

<div class="kartu-gelap p-4 mb-4">
    <h5 class="fw-semibold mb-3 teks-emas">
        <i class="fa-solid fa-signal me-2"></i>Trading Signals Hari Ini
    </h5>
    
    <div class="row g-3">
        <?php foreach ($trading_signals as $signal): ?>
            <div class="col-lg-6 col-md-12">
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-semibold mb-0"><?= $signal['pair'] ?></h6>
                            <small class="text-muted"><?= $signal['timeframe'] ?></small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-<?= getSignalColor($signal['signal']) ?> fs-6">
                                <?= $signal['signal'] ?>
                            </span>
                            <div class="small">
                                <span class="badge bg-<?= getConfidenceColor($signal['confidence']) ?>">
                                    <?= $signal['confidence'] ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mt-2">
                        <div class="col-4">
                            <small class="text-muted d-block">Entry</small>
                            <span class="fw-semibold"><?= $signal['entry'] ?></span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Target</small>
                            <span class="text-success fw-semibold"><?= $signal['target'] ?></span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Stop Loss</small>
                            <span class="text-danger fw-semibold"><?= $signal['stop'] ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-<?= getConfidenceColor($signal['confidence']) ?>" 
                                 style="width: <?= $signal['confidence'] ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-3 text-center">
        <small class="text-muted">
            <i class="fa-solid fa-exclamation-triangle me-1"></i>
            Trading signals hanya untuk referensi. Selalu lakukan analisis sendiri.
        </small>
    </div>
</div>







