<?php
declare(strict_types=1);
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/keamanan.php';
require_once __DIR__ . '/fungsi.php';

/**
 * Middleware untuk proteksi halaman premium
 * Hanya member dengan status 'active' dan membership aktif (LIFETIME - tidak ada expired)
 */
function premiumOnly(): void {
    // Cek apakah user sudah login sebagai member
    if (!isset($_SESSION['member_id'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: ' . basis_url('login.php?pesan=premium_required'));
        exit;
    }
    
    $id_member = $_SESSION['member_id'];
    
    // Cek status member dan membership
    // Premium sekarang LIFETIME - tidak ada expired date, paket, atau status
    $sql = "SELECT m.id_member, m.status_member, 
                   mem.id_membership
            FROM member m
            LEFT JOIN membership mem ON m.id_member = mem.id_member
            WHERE m.id_member = ? 
            ORDER BY mem.id_membership DESC 
            LIMIT 1";
    
    $stmt = jalankan_query_siap($sql, 'i', [$id_member]);
    $hasil = $stmt->get_result();
    
    if ($hasil->num_rows === 0) {
        $stmt->close();
        session_destroy();
        header('Location: ' . basis_url('login.php?pesan=member_not_found'));
        exit;
    }
    
    $member = $hasil->fetch_assoc();
    $stmt->close();
    
    // Cek status member
    if ($member['status_member'] !== 'active') {
        switch($member['status_member']) {
            case 'pending':
                $pesan = 'Akun Anda masih menunggu verifikasi admin. Setelah verifikasi berhasil, Anda dapat login sebagai member premium.';
                break;
            case 'rejected':
                $pesan = 'Akun Anda ditolak. Silakan hubungi admin untuk informasi lebih lanjut.';
                break;
            default:
                $pesan = 'Anda belum memiliki akses premium. Silakan berlangganan untuk melanjutkan.';
                break;
        }
        
        $_SESSION['pesan_error'] = $pesan;
        header('Location: ' . basis_url(''));
        exit;
    }
    
    // Cek apakah membership ada (Premium = LIFETIME, tidak perlu cek status atau expired)
    if (empty($member['id_membership'])) {
        $_SESSION['pesan_error'] = 'Anda belum memiliki akses premium. Silakan berlangganan untuk melanjutkan.';
        header('Location: ' . basis_url(''));
        exit;
    }
    
    // Premium adalah LIFETIME - tidak ada expired date, tidak perlu cek tanggal
}

/**
 * Cek apakah user adalah member premium aktif
 * Return true jika premium, false jika tidak
 * Premium = LIFETIME (tidak ada expired date)
 */
function isPremiumMember(): bool {
    if (!isset($_SESSION['member_id'])) {
        return false;
    }
    
    $id_member = $_SESSION['member_id'];
    
    $sql = "SELECT m.status_member, mem.id_membership
            FROM member m
            LEFT JOIN membership mem ON m.id_member = mem.id_member
            WHERE m.id_member = ? 
            ORDER BY mem.id_membership DESC 
            LIMIT 1";
    
    $stmt = jalankan_query_siap($sql, 'i', [$id_member]);
    $hasil = $stmt->get_result();
    
    if ($hasil->num_rows === 0) {
        $stmt->close();
        return false;
    }
    
    $member = $hasil->fetch_assoc();
    $stmt->close();
    
    // Premium = LIFETIME, hanya cek status member active dan ada membership
    return ($member['status_member'] === 'active' && 
            !empty($member['id_membership']));
}

/**
 * Get informasi membership user saat ini
 * Premium = LIFETIME (tidak ada expired date)
 */
function getMemberInfo(): ?array {
    if (!isset($_SESSION['member_id'])) {
        return null;
    }
    
    $id_member = $_SESSION['member_id'];
    
    $sql = "SELECT m.*, mem.tanggal_mulai, mem.tanggal_aktivasi
            FROM member m
            LEFT JOIN membership mem ON m.id_member = mem.id_member
            WHERE m.id_member = ? 
            ORDER BY mem.id_membership DESC 
            LIMIT 1";
    
    $stmt = jalankan_query_siap($sql, 'i', [$id_member]);
    $hasil = $stmt->get_result();
    
    if ($hasil->num_rows === 0) {
        $stmt->close();
        return null;
    }
    
    $member = $hasil->fetch_assoc();
    $stmt->close();
    
    return $member;
}

