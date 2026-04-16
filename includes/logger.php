<?php
declare(strict_types=1);

/**
 * Sistem Error Logging untuk ARFXTRADE
 * Menyimpan log error ke file dengan rotasi otomatis
 */

class Logger {
    private static string $log_dir = __DIR__ . '/../logs/';
    private static string $error_log = 'error.log';
    private static string $info_log = 'info.log';
    private static int $max_file_size = 5 * 1024 * 1024; // 5MB
    
    /**
     * Inisialisasi logger
     */
    private static function init(): void {
        if (!is_dir(self::$log_dir)) {
            mkdir(self::$log_dir, 0755, true);
        }
    }
    
    /**
     * Log error
     */
    public static function error(string $message, array $context = []): void {
        self::init();
        $log_message = self::formatMessage('ERROR', $message, $context);
        self::writeLog(self::$error_log, $log_message);
    }
    
    /**
     * Log warning
     */
    public static function warning(string $message, array $context = []): void {
        self::init();
        $log_message = self::formatMessage('WARNING', $message, $context);
        self::writeLog(self::$error_log, $log_message);
    }
    
    /**
     * Log info
     */
    public static function info(string $message, array $context = []): void {
        self::init();
        $log_message = self::formatMessage('INFO', $message, $context);
        self::writeLog(self::$info_log, $log_message);
    }
    
    /**
     * Format pesan log
     */
    private static function formatMessage(string $level, string $message, array $context = []): string {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $user_id = $_SESSION['admin_id'] ?? $_SESSION['member_id'] ?? 'guest';
        
        $log = "[{$timestamp}] [{$level}] [IP: {$ip}] [User: {$user_id}] [URI: {$uri}] {$message}";
        
        if (!empty($context)) {
            $log .= " | Context: " . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        $log .= PHP_EOL;
        return $log;
    }
    
    /**
     * Tulis log ke file dengan rotasi
     */
    private static function writeLog(string $filename, string $message): void {
        $file_path = self::$log_dir . $filename;
        
        // Rotasi file jika terlalu besar
        if (file_exists($file_path) && filesize($file_path) > self::$max_file_size) {
            $backup_path = self::$log_dir . date('Y-m-d_His') . '_' . $filename;
            rename($file_path, $backup_path);
            
            // Hapus backup lama (lebih dari 30 hari)
            $files = glob(self::$log_dir . '*_' . $filename);
            foreach ($files as $file) {
                if (filemtime($file) < time() - (30 * 24 * 60 * 60)) {
                    @unlink($file);
                }
            }
        }
        
        file_put_contents($file_path, $message, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log database error
     */
    public static function dbError(string $message, string $sql = '', array $params = []): void {
        self::error($message, [
            'sql' => $sql,
            'params' => $params
        ]);
    }
}









