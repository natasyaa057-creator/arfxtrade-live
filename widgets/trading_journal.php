<?php
/**
 * Widget Trading Journal / Riwayat Transaksi
 * Menampilkan tabel transaksi dengan: Tanggal, Pair, Tipe, Entry-SL-TP, Hasil, Catatan
 */

require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/fungsi.php';

// Ambil data trading journal
$trading_journal = [];
try {
    $sql = "SELECT * FROM trading_journal ORDER BY tanggal DESC, id_transaksi DESC LIMIT 50";
    $stmt = jalankan_query_siap($sql, '', []);
    $result = $stmt->get_result();
    $trading_journal = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    // Data dummy untuk demo
    $trading_journal = [
        [
            'id_transaksi' => 1,
            'tanggal' => '2024-12-15',
            'pair' => 'XAUUSD',
            'tipe_order' => 'BUY',
            'entry_price' => 4200.50,
            'stop_loss' => 4180.00,
            'take_profit' => 4240.00,
            'hasil' => 'WIN',
            'profit_loss' => 150.25,
            'risk_reward_ratio' => 2.0,
            'catatan' => 'Breakout dari resistance level'
        ],
        [
            'id_transaksi' => 2,
            'tanggal' => '2024-12-14',
            'pair' => 'EURUSD',
            'tipe_order' => 'SELL',
            'entry_price' => 1.0850,
            'stop_loss' => 1.0880,
            'take_profit' => 1.0790,
            'hasil' => 'WIN',
            'profit_loss' => 85.50,
            'risk_reward_ratio' => 2.0,
            'catatan' => 'Rejection dari resistance'
        ],
        [
            'id_transaksi' => 3,
            'tanggal' => '2024-12-13',
            'pair' => 'GBPUSD',
            'tipe_order' => 'BUY',
            'entry_price' => 1.2650,
            'stop_loss' => 1.2620,
            'take_profit' => 1.2710,
            'hasil' => 'LOSS',
            'profit_loss' => -30.00,
            'risk_reward_ratio' => 2.0,
            'catatan' => 'False breakout'
        ]
    ];
}
?>

<div class="kartu-gelap p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0 teks-emas">
            <i class="fa-solid fa-book me-2"></i>Trading Journal / Riwayat Transaksi
        </h5>
        <span class="badge bg-secondary"><?= count($trading_journal) ?> Transaksi</span>
    </div>
    
    <div class="table-responsive">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pair</th>
                    <th>Tipe</th>
                    <th>Entry</th>
                    <th>SL</th>
                    <th>TP</th>
                    <th>RR</th>
                    <th>Hasil</th>
                    <th>P/L</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trading_journal)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada transaksi
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($trading_journal as $transaksi): ?>
                        <tr>
                            <td>
                                <small><?= date('d M Y', strtotime($transaksi['tanggal'])) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= aman_html($transaksi['pair']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $transaksi['tipe_order'] === 'BUY' ? 'success' : 'danger' ?>">
                                    <?= aman_html($transaksi['tipe_order']) ?>
                                </span>
                            </td>
                            <td>
                                <small class="fw-semibold"><?= number_format($transaksi['entry_price'], 5) ?></small>
                            </td>
                            <td>
                                <small class="text-danger"><?= number_format($transaksi['stop_loss'], 5) ?></small>
                            </td>
                            <td>
                                <small class="text-success"><?= number_format($transaksi['take_profit'], 5) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= number_format($transaksi['risk_reward_ratio'], 2) ?>:1</span>
                            </td>
                            <td>
                                <?php if ($transaksi['hasil'] === 'WIN'): ?>
                                    <span class="badge bg-success">
                                        <i class="fa-solid fa-check"></i> WIN
                                    </span>
                                <?php elseif ($transaksi['hasil'] === 'LOSS'): ?>
                                    <span class="badge bg-danger">
                                        <i class="fa-solid fa-times"></i> LOSS
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">BE</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fw-semibold <?= $transaksi['profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $transaksi['profit_loss'] >= 0 ? '+' : '' ?><?= number_format($transaksi['profit_loss'], 2) ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted" title="<?= aman_html($transaksi['catatan'] ?? '') ?>">
                                    <?= aman_html(substr($transaksi['catatan'] ?? '', 0, 30)) ?>
                                    <?= strlen($transaksi['catatan'] ?? '') > 30 ? '...' : '' ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>








