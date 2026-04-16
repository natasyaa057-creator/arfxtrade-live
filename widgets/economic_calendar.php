<?php
// Widget Economic Calendar untuk halaman analisis
$economic_events = [
    [
        'time' => '14:30',
        'currency' => 'USD',
        'event' => 'Non-Farm Payrolls',
        'impact' => 'high',
        'forecast' => '180K',
        'previous' => '175K'
    ],
    [
        'time' => '15:00',
        'currency' => 'EUR',
        'event' => 'ECB Interest Rate Decision',
        'impact' => 'high',
        'forecast' => '4.25%',
        'previous' => '4.00%'
    ],
    [
        'time' => '16:30',
        'currency' => 'GBP',
        'event' => 'GDP Growth Rate',
        'impact' => 'medium',
        'forecast' => '0.3%',
        'previous' => '0.2%'
    ],
    [
        'time' => '20:00',
        'currency' => 'JPY',
        'event' => 'Bank of Japan Policy Rate',
        'impact' => 'medium',
        'forecast' => '-0.10%',
        'previous' => '-0.10%'
    ]
];

function getImpactColor($impact) {
    switch($impact) {
        case 'high': return 'danger';
        case 'medium': return 'warning';
        case 'low': return 'success';
        default: return 'secondary';
    }
}
?>

<div class="kartu-gelap p-4 mb-4">
    <h5 class="fw-semibold mb-3 teks-emas">
        <i class="fa-solid fa-calendar-days me-2"></i>Economic Calendar Hari Ini
    </h5>
    
    <div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Mata Uang</th>
                    <th>Event</th>
                    <th>Impact</th>
                    <th>Forecast</th>
                    <th>Previous</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($economic_events as $event): ?>
                    <tr>
                        <td class="fw-semibold"><?= $event['time'] ?></td>
                        <td>
                            <span class="badge bg-primary"><?= $event['currency'] ?></span>
                        </td>
                        <td><?= $event['event'] ?></td>
                        <td>
                            <span class="badge bg-<?= getImpactColor($event['impact']) ?>">
                                <?= strtoupper($event['impact']) ?>
                            </span>
                        </td>
                        <td class="text-success"><?= $event['forecast'] ?></td>
                        <td class="text-muted"><?= $event['previous'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="mt-3 text-center">
        <a href="#" class="btn btn-outline-light btn-sm">
            <i class="fa-solid fa-calendar me-1"></i>Lihat Kalender Lengkap
        </a>
    </div>
</div>







