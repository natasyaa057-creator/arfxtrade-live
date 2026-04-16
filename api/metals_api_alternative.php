<?php
/**
 * Alternative API endpoint untuk XAUUSD dengan format berbeda
 * Mencoba beberapa format Metals-API
 */

declare(strict_types=1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/metals_api.php';

// Coba format alternatif
class MetalsAPIAlternative extends MetalsAPI {
    /**
     * Fetch dengan format alternatif (base=XAU, symbols=USD)
     */
    public function getXAUUSDAlternative(): array {
        if (empty(METALS_API_KEY)) {
            return $this->getXAUUSD(); // Fallback ke method biasa
        }
        
        try {
            // Format alternatif: base=XAU, symbols=USD
            // Ini akan memberikan berapa USD yang setara dengan 1 ounce gold
            $url = 'https://metals-api.com/api/latest?access_key=' . METALS_API_KEY . '&base=XAU&symbols=USD';
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ARFXTRADE/1.0');
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200 && $response) {
                $json = json_decode($response, true);
                
                if (isset($json['success']) && $json['success'] === true && isset($json['rates']['USD'])) {
                    // Format ini: base=XAU, rates['USD'] = berapa USD untuk 1 ounce gold
                    // Langsung dapat harga per ounce
                    $xauusd_price = (float)$json['rates']['USD'];
                    
                    // Hitung change dari cache
                    $cached = $this->getCache();
                    $change = 0;
                    $change_percent = 0;
                    
                    if ($cached && isset($cached['data']['price'])) {
                        $old_price = (float)$cached['data']['price'];
                        $change = $xauusd_price - $old_price;
                        $change_percent = ($change / $old_price) * 100;
                    }
                    
                    // Hitung high/low
                    $high = $xauusd_price;
                    $low = $xauusd_price;
                    if ($cached && isset($cached['data']['high']) && isset($cached['data']['low'])) {
                        $high = max($xauusd_price, (float)$cached['data']['high']);
                        $low = min($xauusd_price, (float)$cached['data']['low']);
                    } else {
                        $high = $xauusd_price * 1.005;
                        $low = $xauusd_price * 0.995;
                    }
                    
                    $data = [
                        'symbol' => 'XAUUSD',
                        'price' => round($xauusd_price, 2),
                        'change' => round($change, 2),
                        'change_percent' => round($change_percent, 2),
                        'high' => round($high, 2),
                        'low' => round($low, 2),
                        'timestamp' => time(),
                        'date' => $json['date'] ?? date('Y-m-d')
                    ];
                    
                    $this->saveCache($data);
                    
                    return [
                        'success' => true,
                        'data' => $data,
                        'cached' => false,
                        'source' => 'metals-api.com (alternative format)'
                    ];
                }
            }
        } catch (Exception $e) {
            error_log('Metals-API Alternative error: ' . $e->getMessage());
        }
        
        // Fallback ke method biasa
        return $this->getXAUUSD();
    }
}

// Handle request
$metals = new MetalsAPIAlternative();
$result = $metals->getXAUUSDAlternative();

echo json_encode($result, JSON_PRETTY_PRINT);








