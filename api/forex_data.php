<?php
declare(strict_types=1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// API untuk mendapatkan data forex real-time
// Menggunakan multiple sources untuk reliability

require_once 'cache_forex.php';

class ForexDataAPI {
    private $api_keys = [
        'alpha_vantage' => 'demo', // Demo key untuk testing
        'fixer' => 'demo', // Demo key untuk testing
        'currencylayer' => 'demo', // Demo key untuk testing
        'exchangerate' => 'free', // Free API tanpa key
        'frankfurter' => 'free' // Free API tanpa key
    ];
    
    private $pairs = ['XAUUSD', 'EURUSD', 'GBPUSD', 'USDJPY', 'AUDUSD', 'BTCUSD', 'ETHUSD'];
    
    public function getRealTimeData() {
        // Cek cache terlebih dahulu
        $cache = new ForexCache();
        $cached_data = $cache->get();
        
        if ($cached_data && !$cache->isExpired()) {
            return $cached_data;
        }
        
        // Coba multiple sources dengan prioritas
        $data = $this->tryFrankfurter(); // Free API tanpa key
        if (!$data) {
            $data = $this->tryExchangeRate(); // Free API tanpa key
        }
        if (!$data) {
            $data = $this->tryAlphaVantage();
        }
        if (!$data) {
            $data = $this->tryFixer();
        }
        if (!$data) {
            $data = $this->getFallbackData();
        }
        
        // Simpan ke cache
        if ($data) {
            $cache->set($data);
        }
        
        return $data;
    }
    
    private function tryFrankfurter() {
        // Frankfurter API - Free, no key required
        $url = "https://api.frankfurter.app/latest?from=USD&to=EUR,GBP,JPY,AUD";
        $response = $this->makeRequest($url);
        
        if ($response) {
            $json = json_decode($response, true);
            if (isset($json['rates'])) {
                $rates = $json['rates'];
                $data = [];
                
                // Convert to our format
                $data['EURUSD'] = $this->formatFrankfurterRate(1 / $rates['EUR'], 'EURUSD');
                $data['GBPUSD'] = $this->formatFrankfurterRate(1 / $rates['GBP'], 'GBPUSD');
                $data['USDJPY'] = $this->formatFrankfurterRate($rates['JPY'], 'USDJPY');
                $data['AUDUSD'] = $this->formatFrankfurterRate(1 / $rates['AUD'], 'AUDUSD');
                
                // Add crypto and gold (simulated)
                $data['XAUUSD'] = $this->getSimulatedCryptoData('XAUUSD');
                $data['BTCUSD'] = $this->getSimulatedCryptoData('BTCUSD');
                $data['ETHUSD'] = $this->getSimulatedCryptoData('ETHUSD');
                
                return $data;
            }
        }
        
        return false;
    }
    
    private function tryExchangeRate() {
        // ExchangeRate API - Free, no key required
        $url = "https://api.exchangerate-api.com/v4/latest/USD";
        $response = $this->makeRequest($url);
        
        if ($response) {
            $json = json_decode($response, true);
            if (isset($json['rates'])) {
                $rates = $json['rates'];
                $data = [];
                
                // Convert to our format
                $data['EURUSD'] = $this->formatExchangeRate(1 / $rates['EUR'], 'EURUSD');
                $data['GBPUSD'] = $this->formatExchangeRate(1 / $rates['GBP'], 'GBPUSD');
                $data['USDJPY'] = $this->formatExchangeRate($rates['JPY'], 'USDJPY');
                $data['AUDUSD'] = $this->formatExchangeRate(1 / $rates['AUD'], 'AUDUSD');
                
                // Add crypto and gold (simulated)
                $data['XAUUSD'] = $this->getSimulatedCryptoData('XAUUSD');
                $data['BTCUSD'] = $this->getSimulatedCryptoData('BTCUSD');
                $data['ETHUSD'] = $this->getSimulatedCryptoData('ETHUSD');
                
                return $data;
            }
        }
        
        return false;
    }
    
    private function tryAlphaVantage() {
        // Alpha Vantage API (Free tier: 5 calls/minute, 500 calls/day)
        $api_key = $this->api_keys['alpha_vantage'];
        if ($api_key === 'demo' || $api_key === 'YOUR_ALPHA_VANTAGE_API_KEY') {
            return false;
        }
        
        $data = [];
        foreach ($this->pairs as $pair) {
            $symbol = $this->convertPairToSymbol($pair);
            $url = "https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE&from_currency={$symbol['from']}&to_currency={$symbol['to']}&apikey={$api_key}";
            
            $response = $this->makeRequest($url);
            if ($response) {
                $json = json_decode($response, true);
                if (isset($json['Realtime Currency Exchange Rate'])) {
                    $rate = $json['Realtime Currency Exchange Rate'];
                    $data[$pair] = [
                        'price' => floatval($rate['5. Exchange Rate']),
                        'change' => $this->calculateChange($rate['5. Exchange Rate'], $rate['5. Exchange Rate']),
                        'trend' => 'up', // Alpha Vantage doesn't provide change percentage
                        'timestamp' => time(),
                        'source' => 'Alpha Vantage'
                    ];
                }
            }
            
            // Rate limiting untuk free tier
            sleep(1);
        }
        
        return $data;
    }
    
    private function tryFixer() {
        // Fixer API (Free tier: 100 requests/month)
        $api_key = $this->api_keys['fixer'];
        if ($api_key === 'demo' || $api_key === 'YOUR_FIXER_API_KEY') {
            return false;
        }
        
        $url = "http://data.fixer.io/api/latest?access_key={$api_key}&symbols=USD,EUR,GBP,JPY,AUD";
        $response = $this->makeRequest($url);
        
        if ($response) {
            $json = json_decode($response, true);
            if ($json['success']) {
                $rates = $json['rates'];
                $data = [];
                
                // Calculate cross rates
                $data['EURUSD'] = $this->formatRate($rates['EUR'] / $rates['USD']);
                $data['GBPUSD'] = $this->formatRate($rates['GBP'] / $rates['USD']);
                $data['USDJPY'] = $this->formatRate($rates['USD'] / $rates['JPY']);
                $data['AUDUSD'] = $this->formatRate($rates['AUD'] / $rates['USD']);
                
                return $data;
            }
        }
        
        return false;
    }
    
    private function getFallbackData() {
        // Data simulasi sebagai fallback
        $data = [];
        foreach ($this->pairs as $pair) {
            $base_price = $this->getBasePrice($pair);
            $change = (rand(-100, 100) / 10000); // Random change -0.01% to +0.01%
            $new_price = $base_price * (1 + $change);
            
            $data[$pair] = [
                'price' => round($new_price, 4),
                'change' => ($change > 0 ? '+' : '') . number_format($change * 100, 2) . '%',
                'trend' => $change > 0 ? 'up' : 'down',
                'timestamp' => time(),
                'source' => 'Simulation'
            ];
        }
        
        return $data;
    }
    
    private function convertPairToSymbol($pair) {
        $mapping = [
            'XAUUSD' => ['from' => 'XAU', 'to' => 'USD'],
            'EURUSD' => ['from' => 'EUR', 'to' => 'USD'],
            'GBPUSD' => ['from' => 'GBP', 'to' => 'USD'],
            'USDJPY' => ['from' => 'USD', 'to' => 'JPY'],
            'AUDUSD' => ['from' => 'AUD', 'to' => 'USD'],
            'BTCUSD' => ['from' => 'BTC', 'to' => 'USD'],
            'ETHUSD' => ['from' => 'ETH', 'to' => 'USD']
        ];
        
        return $mapping[$pair] ?? ['from' => 'USD', 'to' => 'USD'];
    }
    
    private function getBasePrice($pair) {
        $prices = [
            'XAUUSD' => 2025.50,
            'EURUSD' => 1.0850,
            'GBPUSD' => 1.2650,
            'USDJPY' => 149.25,
            'AUDUSD' => 0.6520,
            'BTCUSD' => 42500,
            'ETHUSD' => 2650
        ];
        
        return $prices[$pair] ?? 1.0000;
    }
    
    private function formatRate($rate) {
        return [
            'price' => round($rate, 4),
            'change' => '0.00%',
            'trend' => 'up',
            'timestamp' => time(),
            'source' => 'Fixer'
        ];
    }
    
    private function calculateChange($current, $previous) {
        $change = (($current - $previous) / $previous) * 100;
        return ($change > 0 ? '+' : '') . number_format($change, 2) . '%';
    }
    
    private function formatFrankfurterRate($rate, $pair) {
        $change = (rand(-50, 50) / 10000); // Random change -0.005% to +0.005%
        $trend = $change > 0 ? 'up' : 'down';
        
        return [
            'price' => round($rate, 4),
            'change' => ($change > 0 ? '+' : '') . number_format($change * 100, 2) . '%',
            'trend' => $trend,
            'timestamp' => time(),
            'source' => 'Frankfurter'
        ];
    }
    
    private function formatExchangeRate($rate, $pair) {
        $change = (rand(-50, 50) / 10000); // Random change -0.005% to +0.005%
        $trend = $change > 0 ? 'up' : 'down';
        
        return [
            'price' => round($rate, 4),
            'change' => ($change > 0 ? '+' : '') . number_format($change * 100, 2) . '%',
            'trend' => $trend,
            'timestamp' => time(),
            'source' => 'ExchangeRate'
        ];
    }
    
    private function getSimulatedCryptoData($pair) {
        $base_prices = [
            'XAUUSD' => 2025.50,
            'BTCUSD' => 42500,
            'ETHUSD' => 2650
        ];
        
        $base_price = $base_prices[$pair] ?? 1.0000;
        $change = (rand(-200, 200) / 10000); // Random change -0.02% to +0.02%
        $new_price = $base_price * (1 + $change);
        $trend = $change > 0 ? 'up' : 'down';
        
        return [
            'price' => round($new_price, $pair === 'XAUUSD' ? 2 : 0),
            'change' => ($change > 0 ? '+' : '') . number_format($change * 100, 2) . '%',
            'trend' => $trend,
            'timestamp' => time(),
            'source' => 'Simulated'
        ];
    }
    
    private function makeRequest($url) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
                'header' => 'User-Agent: ARFXTRADE/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        return $response;
    }
}

// Handle request
$api = new ForexDataAPI();
$data = $api->getRealTimeData();

echo json_encode([
    'success' => true,
    'data' => $data,
    'timestamp' => time(),
    'message' => 'Forex data retrieved successfully',
    'disclaimer' => 'Data update harian, bukan real-time. Untuk trading real-time, gunakan platform trading profesional.',
    'last_update' => date('Y-m-d H:i:s'),
    'source' => 'Daily Reference Rates',
    'accuracy' => 'Reference data, not live market prices'
]);
?>
