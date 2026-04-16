<?php
declare(strict_types=1);
require_once __DIR__ . '/koneksi.php';

/**
 * Sistem Notifikasi untuk Membership
 * Mendukung Email dan WhatsApp
 */

// Konfigurasi (sesuaikan dengan setup Anda)
define('WHATSAPP_API_URL', 'https://api.whatsapp.com/send'); // Ganti dengan API WhatsApp Anda
define('WHATSAPP_API_KEY', ''); // Masukkan API key WhatsApp
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', ''); // Email pengirim
define('SMTP_PASS', ''); // Password email
define('SMTP_FROM_EMAIL', 'noreply@arfxtrade.com');
define('SMTP_FROM_NAME', 'ARFXTRADE');

/**
 * Kirim notifikasi verifikasi berhasil
 */
function kirim_notifikasi_verifikasi_berhasil(int $id_member): void {
    $sql = "SELECT nama_lengkap, email, nomor_whatsapp, mem.tanggal_aktivasi
            FROM member m
            JOIN membership mem ON m.id_member = mem.id_member
            WHERE m.id_member = ?
            ORDER BY mem.tanggal_aktivasi DESC LIMIT 1";
    
    $stmt = jalankan_query_siap($sql, 'i', [$id_member]);
    $hasil = $stmt->get_result();
    
    if ($hasil->num_rows === 0) {
        $stmt->close();
        return;
    }
    
    $data = $hasil->fetch_assoc();
    $stmt->close();
    
    $pesan = "Selamat {$data['nama_lengkap']}! Akun premium Anda telah aktif.\n\n";
    $pesan .= "Paket: Premium Lifetime (Sekali Bayar)\n";
    $pesan .= "Akses: Lifetime (Seumur Hidup)\n";
    $pesan .= "Tanggal Aktivasi: " . date('d F Y', strtotime($data['tanggal_aktivasi'])) . "\n\n";
    $pesan .= "Sekarang Anda dapat mengakses semua fitur premium di ARFXTRADE.";
    
    // Simpan ke tabel notifikasi
    $sql_notif = "INSERT INTO notifikasi_member (id_member, jenis, pesan, dikirim_via, status_kirim) 
                  VALUES (?, 'verifikasi_berhasil', ?, 'both', 'pending')";
    jalankan_query_siap($sql_notif, 'is', [$id_member, $pesan]);
    
    // Kirim email
    kirim_email($data['email'], 'Verifikasi Berhasil - ARFXTRADE', $pesan);
    
    // Kirim WhatsApp
    kirim_whatsapp($data['nomor_whatsapp'], $pesan);
}

/**
 * Kirim notifikasi verifikasi ditolak
 */
function kirim_notifikasi_verifikasi_ditolak(int $id_member, string $alasan): void {
    $sql = "SELECT nama_lengkap, email, nomor_whatsapp FROM member WHERE id_member = ?";
    $stmt = jalankan_query_siap($sql, 'i', [$id_member]);
    $hasil = $stmt->get_result();
    
    if ($hasil->num_rows === 0) {
        $stmt->close();
        return;
    }
    
    $data = $hasil->fetch_assoc();
    $stmt->close();
    
    $pesan = "Maaf {$data['nama_lengkap']}, verifikasi pembayaran Anda ditolak.\n\n";
    $pesan .= "Alasan: {$alasan}\n\n";
    $pesan .= "Silakan periksa bukti pembayaran Anda dan hubungi admin jika ada pertanyaan.";
    
    // Simpan ke tabel notifikasi
    $sql_notif = "INSERT INTO notifikasi_member (id_member, jenis, pesan, dikirim_via, status_kirim) 
                  VALUES (?, 'verifikasi_ditolak', ?, 'both', 'pending')";
    jalankan_query_siap($sql_notif, 'is', [$id_member, $pesan]);
    
    // Kirim email
    kirim_email($data['email'], 'Verifikasi Ditolak - ARFXTRADE', $pesan);
    
    // Kirim WhatsApp
    kirim_whatsapp($data['nomor_whatsapp'], $pesan);
}

/**
 * Kirim notifikasi masa aktif hampir habis
 */
/**
 * Kirim notifikasi masa aktif hampir habis
 * CATATAN: Fungsi ini tidak digunakan untuk Premium Lifetime karena tidak ada expired date
 * Tetap dipertahankan untuk kompatibilitas jika diperlukan di masa depan
 */
function kirim_notifikasi_hampir_habis(int $id_member, int $sisa_hari): void {
    // Premium Lifetime tidak memiliki expired date, fungsi ini tidak digunakan
    // Tetap dipertahankan untuk kompatibilitas
    return;
}

/**
 * Kirim notifikasi masa aktif habis
 * CATATAN: Fungsi ini tidak digunakan untuk Premium Lifetime karena tidak ada expired date
 * Tetap dipertahankan untuk kompatibilitas jika diperlukan di masa depan
 */
function kirim_notifikasi_expired(int $id_member): void {
    // Premium Lifetime tidak memiliki expired date, fungsi ini tidak digunakan
    // Tetap dipertahankan untuk kompatibilitas
    return;
}

/**
 * Fungsi helper untuk kirim email
 */
function kirim_email(string $to, string $subject, string $message): bool {
    require_once __DIR__ . '/logger.php';
    
    // Cek apakah SMTP dikonfigurasi
    if (empty(SMTP_USER) || empty(SMTP_PASS)) {
        // Jika tidak dikonfigurasi, log saja (untuk development)
        Logger::info('Email not sent - SMTP not configured', [
            'to' => $to,
            'subject' => $subject
        ]);
        return false;
    }
    
    // Implementasi email menggunakan mail() native dengan error handling
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    $html_message = "<!DOCTYPE html><html><head><meta charset='UTF-8'></head><body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>";
    $html_message .= "<div style='max-width: 600px; margin: 0 auto; padding: 20px;'>";
    $html_message .= "<div style='background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); color: #d4af37; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;'>";
    $html_message .= "<h1 style='margin: 0;'>ARFXTRADE</h1>";
    $html_message .= "</div>";
    $html_message .= "<div style='background: #fff; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px;'>";
    $html_message .= "<h2 style='color: #0a0a0a; margin-top: 0;'>{$subject}</h2>";
    $html_message .= "<div style='color: #555;'>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</div>";
    $html_message .= "</div>";
    $html_message .= "<div style='text-align: center; padding: 20px; color: #999; font-size: 12px;'>";
    $html_message .= "<p>ARFXTRADE - Premium Trading Platform</p>";
    $html_message .= "<p>Email ini dikirim otomatis, mohon tidak membalas email ini.</p>";
    $html_message .= "</div>";
    $html_message .= "</div></body></html>";
    
    $result = @mail($to, $subject, $html_message, $headers);
    
    if ($result) {
        Logger::info('Email sent successfully', [
            'to' => $to,
            'subject' => $subject
        ]);
    } else {
        Logger::error('Failed to send email', [
            'to' => $to,
            'subject' => $subject,
            'error' => error_get_last()
        ]);
    }
    
    return $result;
}

/**
 * Fungsi helper untuk kirim WhatsApp
 */
function kirim_whatsapp(string $nomor, string $pesan): bool {
    require_once __DIR__ . '/logger.php';
    
    // Sanitasi nomor
    $nomor_bersih = preg_replace('/[^0-9]/', '', $nomor);
    
    if (empty($nomor_bersih)) {
        Logger::warning('WhatsApp not sent - invalid number', ['nomor' => $nomor]);
        return false;
    }
    
    // Jika tidak ada API key, simpan ke log untuk manual sending
    if (empty(WHATSAPP_API_KEY)) {
        // Generate WhatsApp web link
        $pesan_encoded = urlencode($pesan);
        $url = "https://wa.me/{$nomor_bersih}?text={$pesan_encoded}";
        
        Logger::info('WhatsApp link generated (API not configured)', [
            'nomor' => $nomor_bersih,
            'url' => $url,
            'message' => $pesan
        ]);
        
        // Simpan ke database untuk tracking (opsional)
        // Bisa ditambahkan tabel untuk queue WhatsApp messages
        
        return true; // Return true karena link sudah dibuat
    }
    
    // Implementasi dengan API key (sesuaikan dengan provider Anda)
    // Contoh menggunakan cURL untuk WhatsApp Business API
    try {
        $data = [
            'to' => $nomor_bersih,
            'message' => $pesan,
            'type' => 'text'
        ];
        
        $ch = curl_init(WHATSAPP_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . WHATSAPP_API_KEY
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($http_code === 200) {
            Logger::info('WhatsApp sent successfully', [
                'nomor' => $nomor_bersih,
                'response' => $response
            ]);
            return true;
        } else {
            Logger::error('Failed to send WhatsApp', [
                'nomor' => $nomor_bersih,
                'http_code' => $http_code,
                'response' => $response,
                'curl_error' => $curl_error
            ]);
            return false;
        }
    } catch (Exception $e) {
        Logger::error('Exception sending WhatsApp', [
            'nomor' => $nomor_bersih,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}





