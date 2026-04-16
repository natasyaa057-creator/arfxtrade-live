<?php
declare(strict_types=1);

// Cache system untuk data forex
class ForexCache {
    private $cache_dir = 'cache/';
    private $cache_file = 'forex_data.json';
    private $cache_duration = 60; // 60 detik
    
    public function __construct() {
        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }
    
    public function get() {
        $file_path = $this->cache_dir . $this->cache_file;
        
        if (file_exists($file_path)) {
            $data = json_decode(file_get_contents($file_path), true);
            $age = time() - $data['timestamp'];
            
            if ($age < $this->cache_duration) {
                return $data['data'];
            }
        }
        
        return false;
    }
    
    public function set($data) {
        $file_path = $this->cache_dir . $this->cache_file;
        $cache_data = [
            'data' => $data,
            'timestamp' => time()
        ];
        
        file_put_contents($file_path, json_encode($cache_data));
    }
    
    public function clear() {
        $file_path = $this->cache_dir . $this->cache_file;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    public function isExpired() {
        $file_path = $this->cache_dir . $this->cache_file;
        
        if (!file_exists($file_path)) {
            return true;
        }
        
        $data = json_decode(file_get_contents($file_path), true);
        $age = time() - $data['timestamp'];
        
        return $age >= $this->cache_duration;
    }
}
?>







