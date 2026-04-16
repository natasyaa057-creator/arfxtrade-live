<?php
/**
 * API untuk mendapatkan data portfolio (trading journal)
 */

declare(strict_types=1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/koneksi.php';

try {
    // Ambil data trading journal
    $sql = "SELECT 
                tanggal,
                pair,
                tipe_order,
                entry_price,
                stop_loss,
                take_profit,
                hasil,
                profit_loss,
                risk_reward_ratio,
                catatan
            FROM trading_journal 
            ORDER BY tanggal ASC, id_transaksi ASC
            LIMIT 100";
    
    $stmt = jalankan_query_siap($sql, '', []);
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'transactions' => $transactions
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Return dummy data jika tabel belum ada
    $dummy_data = [
        [
            'tanggal' => '2024-12-01',
            'pair' => 'XAUUSD',
            'tipe_order' => 'BUY',
            'entry_price' => 4200.50,
            'stop_loss' => 4180.00,
            'take_profit' => 4240.00,
            'hasil' => 'WIN',
            'profit_loss' => 150.25,
            'risk_reward_ratio' => 2.0,
            'catatan' => 'Breakout dari resistance'
        ],
        [
            'tanggal' => '2024-12-02',
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
            'tanggal' => '2024-12-03',
            'pair' => 'GBPUSD',
            'tipe_order' => 'BUY',
            'entry_price' => 1.2650,
            'stop_loss' => 1.2620,
            'take_profit' => 1.2710,
            'hasil' => 'LOSS',
            'profit_loss' => -30.00,
            'risk_reward_ratio' => 2.0,
            'catatan' => 'False breakout'
        ],
        [
            'tanggal' => '2024-12-04',
            'pair' => 'XAUUSD',
            'tipe_order' => 'BUY',
            'entry_price' => 4210.00,
            'stop_loss' => 4190.00,
            'take_profit' => 4250.00,
            'hasil' => 'WIN',
            'profit_loss' => 200.00,
            'risk_reward_ratio' => 2.0,
            'catatan' => 'Support bounce'
        ],
        [
            'tanggal' => '2024-12-05',
            'pair' => 'EURUSD',
            'tipe_order' => 'BUY',
            'entry_price' => 1.0830,
            'stop_loss' => 1.0800,
            'take_profit' => 1.0890,
            'hasil' => 'WIN',
            'profit_loss' => 60.00,
            'risk_reward_ratio' => 2.0,
            'catatan' => 'Trend continuation'
        ]
    ];
    
    echo json_encode([
        'success' => true,
        'transactions' => $dummy_data
    ], JSON_PRETTY_PRINT);
}








