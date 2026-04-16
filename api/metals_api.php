<?php
/**
 * API untuk mendapatkan harga XAUUSD (Gold) realtime dari Metals-API
 * Metals-API: https://metals-api.com/
 */

declare(strict_types=1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Cache system untuk Metals API

// Load konfigurasi dari file terpisah
$config_file = __DIR__ . '/config_metals_api.php';
if (file_exists($config_file)) {
    require_once $config_file;
} else {
    // Fallback: gunakan konfigurasi default jika file config tidak ada
    define('METALS_API_KEY', '');
    define('METALS_API_URL', 'https://metals-api.com/api/latest');
    define('METALS_CACHE_TIME', 30);
}

class MetalsAPI {
    private $cache_file = __DIR__ . '/cache/metals_data.json';
    
    /**
     * Get harga XAUUSD realtime
     */
    public function getXAUUSD(): array {
        // Cek cache
        $cached = $this->getCache();
        if ($cached && !$this->isCacheExpired($cached)) {
            return [
                'success' => true,
                'data' => $cached['data'],
                'cached' => true,
                'timestamp' => $cached['timestamp']
            ];
        }
        
        // Prioritas 1: Gunakan Metals-API jika ada API key
        if (!empty(METALS_API_KEY)) {
            // Coba format alternatif dulu (base=XAU, symbols=USD) - lebih langsung
            $data_alt = $this->fetchFromMetalsAPIAlternative();
            if ($data_alt['success']) {
                $this->saveCache($data_alt['data']);
                return $data_alt;
            }
            
            // Jika alternatif gagal, coba format standar
            $data = $this->fetchFromMetalsAPI();
            if ($data['success']) {
                $this->saveCache($data['data']);
                return $data;
            }
            // Jika Metals-API gagal, log error tapi tetap lanjut ke fallback
            error_log('Metals-API error: ' . ($data['error'] ?? 'Unknown error'));
        }
        
        // Prioritas 2: Coba API alternatif gratis
        $data = $this->getFallbackData();
        if ($data['source'] !== 'estimated') {
            // Jika dapat dari API alternatif, simpan cache
            $this->saveCache($data['data']);
        }
        return $data;
    }
    
    /**
     * Fetch dari Metals-API dengan format alternatif (base=XAU, symbols=USD)
     * Format ini langsung memberikan harga per ounce dalam USD
     */
    private function fetchFromMetalsAPIAlternative(): array {
        try {
            // Format alternatif: base=XAU, symbols=USD
            // Ini memberikan berapa USD untuk 1 ounce gold (langsung harga per ounce)
            $url = 'https://metals-api.com/api/latest?access_key=' . METALS_API_KEY . '&base=XAU&symbols=USD';
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ARFXTRADE/1.0');
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            if ($http_code === 200 && $response) {
                $json = json_decode($response, true);
                
                // Format: base=XAU, rates['USD'] = harga per ounce dalam USD
                if (isset($json['success']) && $json['success'] === true && isset($json['rates']['USD'])) {
                    $xauusd_price = (float)$json['rates']['USD'];
                    
                    // Validasi harga (harus masuk akal: antara 1000-10000 USD per ounce)
                    if ($xauusd_price < 1000 || $xauusd_price > 10000) {
                        error_log("Metals-API Alternative: Invalid price {$xauusd_price}, trying standard format");
                        return ['success' => false, 'error' => 'Invalid price range'];
                    }
                    
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
                    
                    error_log("Metals-API Alternative: Success - Price = {$xauusd_price}");
                    
                    return [
                        'success' => true,
                        'data' => [
                            'symbol' => 'XAUUSD',
                            'price' => round($xauusd_price, 2),
                            'change' => round($change, 2),
                            'change_percent' => round($change_percent, 2),
                            'high' => round($high, 2),
                            'low' => round($low, 2),
                            'timestamp' => time(),
                            'date' => $json['date'] ?? date('Y-m-d')
                        ],
                        'cached' => false,
                        'source' => 'metals-api.com (base=XAU)'
                    ];
                }
            }
            
            return [
                'success' => false,
                'error' => 'Failed to fetch from Metals-API (alternative format)',
                'http_code' => $http_code,
                'curl_error' => $curl_error
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Fetch dari Metals-API dengan format standar (base=USD, symbols=XAU)
     * Dokumentasi: https://metals-api.com/documentation
     */
    private function fetchFromMetalsAPI(): array {
        try {
            // Metals-API endpoint untuk XAU/USD
            // Format: https://metals-api.com/api/latest?access_key=YOUR_KEY&base=USD&symbols=XAU
            $url = METALS_API_URL . '?access_key=' . METALS_API_KEY . '&base=USD&symbols=XAU';
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ARFXTRADE/1.0');
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            if ($http_code === 200 && $response) {
                $json = json_decode($response, true);
                
                // Metals-API response format:
                // {
                //   "success": true,
                //   "rates": {
                //     "XAU": 0.000377... (1 ounce gold in USD)
                //   },
                //   "base": "USD",
                //   "date": "2024-01-01"
                // }
                
                if (isset($json['success']) && $json['success'] === true && isset($json['rates']['XAU'])) {
                    // Metals-API format: base=USD, symbols=XAU
                    // rates['XAU'] = berapa ounce gold yang bisa dibeli dengan 1 USD
                    // Contoh: rates['XAU'] = 0.00025 berarti $1 = 0.00025 ounce
                    // Maka harga per ounce = 1 / 0.00025 = $4000
                    $xau_rate = (float)$json['rates']['XAU'];
                    
                    // Validasi rate (harus antara 0.0001 dan 0.001 untuk harga normal)
                    if ($xau_rate > 0 && $xau_rate < 1) {
                        $xauusd_price = 1 / $xau_rate; // Harga XAU per ounce dalam USD
                    } else {
                        // Jika format berbeda, coba langsung sebagai harga
                        // Atau mungkin sudah dalam format harga per ounce
                        $xauusd_price = $xau_rate > 1000 ? $xau_rate : (1 / $xau_rate);
                    }
                    
                    // Log untuk debugging
                    error_log("Metals-API Debug: XAU rate = {$xau_rate}, Calculated price = {$xauusd_price}");
                    
                    // Hitung change dari cache sebelumnya
                    $cached = $this->getCache();
                    $change = 0;
                    $change_percent = 0;
                    
                    if ($cached && isset($cached['data']['price'])) {
                        $old_price = (float)$cached['data']['price'];
                        $change = $xauusd_price - $old_price;
                        $change_percent = ($change / $old_price) * 100;
                    }
                    
                    // Hitung high/low dari cache atau estimasi
                    $high = $xauusd_price;
                    $low = $xauusd_price;
                    if ($cached && isset($cached['data']['high']) && isset($cached['data']['low'])) {
                        $high = max($xauusd_price, (float)$cached['data']['high']);
                        $low = min($xauusd_price, (float)$cached['data']['low']);
                    } else {
                        // Estimasi berdasarkan variasi umum
                        $high = $xauusd_price * 1.005;
                        $low = $xauusd_price * 0.995;
                    }
                    
                    return [
                        'success' => true,
                        'data' => [
                            'symbol' => 'XAUUSD',
                            'price' => round($xauusd_price, 2),
                            'change' => round($change, 2),
                            'change_percent' => round($change_percent, 2),
                            'high' => round($high, 2),
                            'low' => round($low, 2),
                            'timestamp' => time(),
                            'date' => $json['date'] ?? date('Y-m-d')
                        ],
                        'cached' => false,
                        'source' => 'metals-api.com'
                    ];
                } elseif (isset($json['error'])) {
                    // API mengembalikan error
                    return [
                        'success' => false,
                        'error' => $json['error']['info'] ?? 'Metals-API error',
                        'error_code' => $json['error']['code'] ?? null
                    ];
                }
            }
            
            // Jika gagal, return error
            return [
                'success' => false,
                'error' => 'Failed to fetch from Metals-API',
                'http_code' => $http_code,
                'curl_error' => $curl_error,
                'response' => substr($response, 0, 200) // First 200 chars for debugging
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Fallback data jika API gagal
     */
    private function getFallbackData(): array {
        // Gunakan API alternatif atau data estimasi
        // Bisa juga menggunakan API gratis lain untuk gold price
        
        // Alternatif: Gunakan API gratis untuk gold price
        $fallback_price = $this->tryAlternativeAPI();
        
        if ($fallback_price) {
            return [
                'success' => true,
                'data' => [
                    'symbol' => 'XAUUSD',
                    'price' => $fallback_price,
                    'change' => rand(-50, 50) / 10,
                    'change_percent' => round(rand(-20, 20) / 100, 2),
                    'high' => round($fallback_price * 1.01, 2),
                    'low' => round($fallback_price * 0.99, 2),
                    'timestamp' => time()
                ],
                'cached' => false,
                'source' => 'fallback'
            ];
        }
        
        // Jika semua gagal, gunakan estimasi berdasarkan harga umum
        // Update harga base ke level yang lebih realistis (sekitar 4000an seperti TradingView)
        $base_price = 4200.00; // Harga estimasi XAUUSD (disesuaikan dengan TradingView)
        $variation = rand(-100, 100) / 10;
        $current_price = $base_price + $variation;
        
        return [
            'success' => true,
            'data' => [
                'symbol' => 'XAUUSD',
                'price' => round($current_price, 2),
                'change' => $variation,
                'change_percent' => round(($variation / $base_price) * 100, 2),
                'high' => round($current_price * 1.005, 2),
                'low' => round($current_price * 0.995, 2),
                'timestamp' => time()
            ],
            'cached' => false,
            'source' => 'estimated'
        ];
    }
    
    /**
     * Coba API alternatif gratis
     */
    private function tryAlternativeAPI(): ?float {
        // Coba beberapa API gratis untuk gold price
        $apis = [
            'https://api.metals.live/v1/spot/XAU/USD', // Metals.live (gratis)
            'https://api.goldapi.io/api/xau/usd', // GoldAPI (perlu key)
        ];
        
        foreach ($apis as $url) {
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($http_code === 200 && $response) {
                    $json = json_decode($response, true);
                    
                    // Parse response berdasarkan format API
                    if (isset($json['price'])) {
                        return (float)$json['price'];
                    } elseif (isset($json['rate'])) {
                        return (float)$json['rate'];
                    } elseif (isset($json['data']['price'])) {
                        return (float)$json['data']['price'];
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        return null;
    }
    
    /**
     * Get cache
     */
    private function getCache(): ?array {
        if (file_exists($this->cache_file)) {
            $data = json_decode(file_get_contents($this->cache_file), true);
            return $data ?: null;
        }
        return null;
    }
    
    /**
     * Check if cache expired
     */
    private function isCacheExpired(array $cache): bool {
        if (!isset($cache['timestamp'])) {
            return true;
        }
        return (time() - $cache['timestamp']) > METALS_CACHE_TIME;
    }
    
    /**
     * Save cache
     */
    private function saveCache(array $data): void {
        $cache_data = [
            'data' => $data,
            'timestamp' => time()
        ];
        file_put_contents($this->cache_file, json_encode($cache_data), LOCK_EX);
    }
}

// Handle request
$metals = new MetalsAPI();
$result = $metals->getXAUUSD();

// Jika request untuk XAUUSD spesifik
if (isset($_GET['symbol']) && strtoupper($_GET['symbol']) === 'XAUUSD') {
    echo json_encode($result, JSON_PRETTY_PRINT);
    exit;
}

// Default: return XAUUSD
echo json_encode($result, JSON_PRETTY_PRINT);

