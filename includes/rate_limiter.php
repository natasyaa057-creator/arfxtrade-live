<?php
declare(strict_types=1);

require_once __DIR__ . '/logger.php';

/**
 * Sistem Rate Limiting untuk mencegah brute force dan spam
 */
class RateLimiter {
    private const RATE_LIMIT_DIR = __DIR__ . '/../cache/rate_limit/';
    private const DEFAULT_MAX_ATTEMPTS = 5;
    private const DEFAULT_WINDOW = 900; // 15 menit dalam detik
    private const LOCKOUT_DURATION = 3600; // 1 jam dalam detik
    
    /**
     * Inisialisasi direktori cache
     */
    private static function init(): void {
        if (!is_dir(self::RATE_LIMIT_DIR)) {
            mkdir(self::RATE_LIMIT_DIR, 0755, true);
        }
    }
    
    /**
     * Cek apakah IP atau identifier sudah melebihi rate limit
     */
    public static function checkLimit(
        string $identifier,
        int $max_attempts = self::DEFAULT_MAX_ATTEMPTS,
        int $window = self::DEFAULT_WINDOW
    ): array {
        self::init();
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = md5($identifier . '_' . $ip);
        $file_path = self::RATE_LIMIT_DIR . $key . '.json';
        
        // Cek apakah ada lockout aktif
        $lockout_file = self::RATE_LIMIT_DIR . $key . '_lockout.json';
        if (file_exists($lockout_file)) {
            $lockout_data = json_decode(file_get_contents($lockout_file), true);
            if ($lockout_data && isset($lockout_data['until'])) {
                if (time() < $lockout_data['until']) {
                    $remaining = $lockout_data['until'] - time();
                    return [
                        'allowed' => false,
                        'message' => 'Terlalu banyak percobaan. Silakan coba lagi dalam ' . 
                                    self::formatTime($remaining) . '.',
                        'remaining_time' => $remaining,
                        'attempts' => $max_attempts
                    ];
                } else {
                    // Lockout sudah expired, hapus file
                    @unlink($lockout_file);
                }
            }
        }
        
        // Baca data rate limit
        $data = [];
        if (file_exists($file_path)) {
            $data = json_decode(file_get_contents($file_path), true) ?: [];
        }
        
        // Reset jika window sudah lewat
        if (isset($data['first_attempt']) && (time() - $data['first_attempt']) > $window) {
            $data = [];
        }
        
        // Hitung attempts
        $attempts = isset($data['attempts']) ? (int)$data['attempts'] : 0;
        
        if ($attempts >= $max_attempts) {
            // Set lockout
            $lockout_data = [
                'until' => time() + self::LOCKOUT_DURATION,
                'identifier' => $identifier,
                'ip' => $ip
            ];
            file_put_contents($lockout_file, json_encode($lockout_data), LOCK_EX);
            
            Logger::warning('Rate limit exceeded - lockout activated', [
                'identifier' => $identifier,
                'ip' => $ip,
                'attempts' => $attempts
            ]);
            
            return [
                'allowed' => false,
                'message' => 'Terlalu banyak percobaan. Akun dikunci selama 1 jam.',
                'remaining_time' => self::LOCKOUT_DURATION,
                'attempts' => $attempts
            ];
        }
        
        return [
            'allowed' => true,
            'message' => '',
            'remaining_attempts' => $max_attempts - $attempts,
            'attempts' => $attempts
        ];
    }
    
    /**
     * Record attempt (gagal)
     */
    public static function recordAttempt(string $identifier): void {
        self::init();
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = md5($identifier . '_' . $ip);
        $file_path = self::RATE_LIMIT_DIR . $key . '.json';
        
        $data = [];
        if (file_exists($file_path)) {
            $data = json_decode(file_get_contents($file_path), true) ?: [];
        }
        
        if (empty($data)) {
            $data = [
                'first_attempt' => time(),
                'attempts' => 0
            ];
        }
        
        $data['attempts'] = ($data['attempts'] ?? 0) + 1;
        $data['last_attempt'] = time();
        
        file_put_contents($file_path, json_encode($data), LOCK_EX);
        
        Logger::info('Rate limit attempt recorded', [
            'identifier' => $identifier,
            'ip' => $ip,
            'attempts' => $data['attempts']
        ]);
    }
    
    /**
     * Reset attempts (berhasil)
     */
    public static function resetAttempts(string $identifier): void {
        self::init();
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = md5($identifier . '_' . $ip);
        $file_path = self::RATE_LIMIT_DIR . $key . '.json';
        $lockout_file = self::RATE_LIMIT_DIR . $key . '_lockout.json';
        
        // Hapus file rate limit dan lockout
        @unlink($file_path);
        @unlink($lockout_file);
    }
    
    /**
     * Format waktu dalam detik ke format readable
     */
    private static function formatTime(int $seconds): string {
        if ($seconds < 60) {
            return $seconds . ' detik';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' menit';
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            if ($minutes > 0) {
                return $hours . ' jam ' . $minutes . ' menit';
            }
            return $hours . ' jam';
        }
    }
    
    /**
     * Cleanup old rate limit files
     */
    public static function cleanup(): void {
        self::init();
        
        $files = glob(self::RATE_LIMIT_DIR . '*.json');
        $now = time();
        
        foreach ($files as $file) {
            // Hapus file yang lebih dari 24 jam
            if (filemtime($file) < ($now - 86400)) {
                @unlink($file);
            }
        }
    }
}









