<?php
declare(strict_types=1);
header('Content-Type: application/json');

// Status API forex dan monitoring
class ForexStatus {
    private $cache_dir = 'cache/';
    private $log_file = 'cache/forex_log.json';
    
    public function getStatus() {
        $status = [
            'timestamp' => time(),
            'cache_status' => $this->getCacheStatus(),
            'api_status' => $this->getAPIStatus(),
            'last_update' => $this->getLastUpdate(),
            'uptime' => $this->getUptime(),
            'requests_today' => $this->getRequestsToday()
        ];
        
        return $status;
    }
    
    private function getCacheStatus() {
        $cache_file = $this->cache_dir . 'forex_data.json';
        
        if (!file_exists($cache_file)) {
            return [
                'status' => 'empty',
                'message' => 'No cached data available'
            ];
        }
        
        $data = json_decode(file_get_contents($cache_file), true);
        $age = time() - $data['timestamp'];
        
        if ($age < 60) {
            return [
                'status' => 'fresh',
                'age' => $age,
                'message' => 'Data is fresh'
            ];
        } else {
            return [
                'status' => 'stale',
                'age' => $age,
                'message' => 'Data needs refresh'
            ];
        }
    }
    
    private function getAPIStatus() {
        // Test external APIs
        $apis = [
            'frankfurter' => 'https://api.frankfurter.app/latest?from=USD&to=EUR',
            'exchangerate' => 'https://api.exchangerate-api.com/v4/latest/USD'
        ];
        
        $results = [];
        
        foreach ($apis as $name => $url) {
            $start_time = microtime(true);
            $response = @file_get_contents($url, false, stream_context_create([
                'http' => ['timeout' => 5]
            ]));
            $end_time = microtime(true);
            
            $results[$name] = [
                'status' => $response ? 'online' : 'offline',
                'response_time' => round(($end_time - $start_time) * 1000, 2),
                'last_check' => time()
            ];
        }
        
        return $results;
    }
    
    private function getLastUpdate() {
        $cache_file = $this->cache_dir . 'forex_data.json';
        
        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            return [
                'timestamp' => $data['timestamp'],
                'formatted' => date('Y-m-d H:i:s', $data['timestamp']),
                'age' => time() - $data['timestamp']
            ];
        }
        
        return null;
    }
    
    private function getUptime() {
        $log_file = $this->log_file;
        
        if (!file_exists($log_file)) {
            return [
                'uptime' => 'unknown',
                'message' => 'No uptime data available'
            ];
        }
        
        $logs = json_decode(file_get_contents($log_file), true);
        $success_count = 0;
        $total_count = count($logs);
        
        foreach ($logs as $log) {
            if ($log['status'] === 'success') {
                $success_count++;
            }
        }
        
        $uptime_percentage = $total_count > 0 ? round(($success_count / $total_count) * 100, 2) : 0;
        
        return [
            'uptime' => $uptime_percentage . '%',
            'successful_requests' => $success_count,
            'total_requests' => $total_count
        ];
    }
    
    private function getRequestsToday() {
        $log_file = $this->log_file;
        
        if (!file_exists($log_file)) {
            return 0;
        }
        
        $logs = json_decode(file_get_contents($log_file), true);
        $today = date('Y-m-d');
        $count = 0;
        
        foreach ($logs as $log) {
            if (date('Y-m-d', $log['timestamp']) === $today) {
                $count++;
            }
        }
        
        return $count;
    }
}

// Handle request
$status = new ForexStatus();
$result = $status->getStatus();

echo json_encode([
    'success' => true,
    'data' => $result,
    'message' => 'Forex API status retrieved successfully'
]);
?>







