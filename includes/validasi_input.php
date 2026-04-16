<?php
declare(strict_types=1);

/**
 * Fungsi validasi input untuk ARFXTRADE
 */

/**
 * Validasi nomor WhatsApp
 */
function validasi_nomor_whatsapp(string $nomor): array {
    // Hapus semua karakter non-digit
    $nomor_bersih = preg_replace('/[^0-9]/', '', $nomor);
    
    // Validasi panjang (min 10, max 15 digit)
    if (strlen($nomor_bersih) < 10 || strlen($nomor_bersih) > 15) {
        return [
            'valid' => false,
            'message' => 'Nomor WhatsApp harus antara 10-15 digit.',
            'nomor_bersih' => ''
        ];
    }
    
    // Validasi format Indonesia (opsional)
    // Format: 08xx atau 628xx
    if (strlen($nomor_bersih) >= 10) {
        // Jika dimulai dengan 0, pastikan digit kedua adalah 8 atau 9
        if ($nomor_bersih[0] === '0' && !in_array($nomor_bersih[1], ['8', '9'])) {
            return [
                'valid' => false,
                'message' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xx atau 628xx.',
                'nomor_bersih' => ''
            ];
        }
        
        // Jika dimulai dengan 62, pastikan digit ketiga adalah 8 atau 9
        if (substr($nomor_bersih, 0, 2) === '62' && !in_array($nomor_bersih[2], ['8', '9'])) {
            return [
                'valid' => false,
                'message' => 'Format nomor WhatsApp tidak valid.',
                'nomor_bersih' => ''
            ];
        }
    }
    
    return [
        'valid' => true,
        'message' => '',
        'nomor_bersih' => $nomor_bersih
    ];
}

/**
 * Validasi username
 */
function validasi_username(string $username): array {
    // Hapus whitespace
    $username = trim($username);
    
    // Validasi panjang
    if (strlen($username) < 3) {
        return [
            'valid' => false,
            'message' => 'Username minimal 3 karakter.'
        ];
    }
    
    if (strlen($username) > 20) {
        return [
            'valid' => false,
            'message' => 'Username maksimal 20 karakter.'
        ];
    }
    
    // Validasi format: hanya alphanumeric, underscore, dan titik
    if (!preg_match('/^[a-zA-Z0-9._]+$/', $username)) {
        return [
            'valid' => false,
            'message' => 'Username hanya boleh mengandung huruf, angka, titik (.), dan underscore (_).'
        ];
    }
    
    // Tidak boleh dimulai atau diakhiri dengan titik atau underscore
    if (preg_match('/^[._]|[._]$/', $username)) {
        return [
            'valid' => false,
            'message' => 'Username tidak boleh dimulai atau diakhiri dengan titik atau underscore.'
        ];
    }
    
    // Tidak boleh mengandung titik atau underscore berturut-turut
    if (preg_match('/[._]{2,}/', $username)) {
        return [
            'valid' => false,
            'message' => 'Username tidak boleh mengandung titik atau underscore berturut-turut.'
        ];
    }
    
    return [
        'valid' => true,
        'message' => '',
        'username_bersih' => strtolower($username)
    ];
}

/**
 * Validasi email
 */
function validasi_email(string $email): array {
    $email = trim($email);
    
    if (empty($email)) {
        return [
            'valid' => false,
            'message' => 'Email harus diisi.'
        ];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'valid' => false,
            'message' => 'Format email tidak valid.'
        ];
    }
    
    // Validasi panjang
    if (strlen($email) > 150) {
        return [
            'valid' => false,
            'message' => 'Email terlalu panjang (maksimal 150 karakter).'
        ];
    }
    
    // Validasi domain (opsional, bisa diaktifkan jika perlu)
    // $domain = substr(strrchr($email, "@"), 1);
    // if (!checkdnsrr($domain, "MX")) {
    //     return ['valid' => false, 'message' => 'Domain email tidak valid.'];
    // }
    
    return [
        'valid' => true,
        'message' => '',
        'email_bersih' => strtolower($email)
    ];
}

/**
 * Validasi password
 */
function validasi_password(string $password, string $confirm_password = ''): array {
    if (empty($password)) {
        return [
            'valid' => false,
            'message' => 'Kata sandi harus diisi.'
        ];
    }
    
    // Validasi panjang
    if (strlen($password) < 6) {
        return [
            'valid' => false,
            'message' => 'Kata sandi minimal 6 karakter.'
        ];
    }
    
    if (strlen($password) > 72) { // bcrypt limit
        return [
            'valid' => false,
            'message' => 'Kata sandi terlalu panjang (maksimal 72 karakter).'
        ];
    }
    
    // Validasi konfirmasi password
    if (!empty($confirm_password) && $password !== $confirm_password) {
        return [
            'valid' => false,
            'message' => 'Kata sandi tidak cocok.'
        ];
    }
    
    return [
        'valid' => true,
        'message' => ''
    ];
}

/**
 * Validasi nama lengkap
 */
function validasi_nama_lengkap(string $nama): array {
    $nama = trim($nama);
    
    if (empty($nama)) {
        return [
            'valid' => false,
            'message' => 'Nama lengkap harus diisi.'
        ];
    }
    
    if (strlen($nama) < 2) {
        return [
            'valid' => false,
            'message' => 'Nama lengkap minimal 2 karakter.'
        ];
    }
    
    if (strlen($nama) > 150) {
        return [
            'valid' => false,
            'message' => 'Nama lengkap terlalu panjang (maksimal 150 karakter).'
        ];
    }
    
    // Validasi: hanya huruf, spasi, dan karakter khusus nama (apostrophe, dash)
    if (!preg_match('/^[\p{L}\s\'-]+$/u', $nama)) {
        return [
            'valid' => false,
            'message' => 'Nama lengkap hanya boleh mengandung huruf, spasi, apostrophe, dan dash.'
        ];
    }
    
    return [
        'valid' => true,
        'message' => '',
        'nama_bersih' => $nama
    ];
}

/**
 * Sanitasi input string
 */
function sanitasi_string(string $input, int $max_length = 255): string {
    $input = trim($input);
    $input = strip_tags($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    if (strlen($input) > $max_length) {
        $input = substr($input, 0, $max_length);
    }
    
    return $input;
}









