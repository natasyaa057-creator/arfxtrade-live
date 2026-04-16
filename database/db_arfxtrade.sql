-- =====================================================
-- Database Schema ARFXTRADE - Complete
-- Sistem Membership Premium Lifetime
-- =====================================================
-- File ini menggabungkan struktur database lengkap
-- dengan perbaikan struktur tabel membership
-- =====================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `db-arfxtrade` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `db-arfxtrade`;

-- =====================================================
-- TABEL: pengguna (Admin)
-- =====================================================
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id_pengguna` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_pengguna` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `kata_sandi` VARCHAR(255) NOT NULL,
  `level` ENUM('admin', 'superadmin') DEFAULT 'admin',
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: member (Member/User Premium)
-- =====================================================
CREATE TABLE IF NOT EXISTS `member` (
  `id_member` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` VARCHAR(150) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `kata_sandi` VARCHAR(255) NOT NULL,
  `nomor_whatsapp` VARCHAR(20) DEFAULT NULL,
  `status_member` ENUM('free', 'pending', 'active', 'expired', 'rejected') DEFAULT 'free',
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_member`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: membership (Riwayat Membership Premium Lifetime)
-- =====================================================
-- Catatan: Sistem menggunakan Premium Lifetime
-- - Tidak ada kolom paket (karena hanya premium lifetime)
-- - Tidak ada kolom tanggal_expired (karena lifetime)
-- - Tidak ada kolom status (karena lifetime selalu aktif)
-- - Status membership ditentukan dari status_member di tabel member
CREATE TABLE IF NOT EXISTS `membership` (
  `id_membership` INT(11) NOT NULL AUTO_INCREMENT,
  `id_member` INT(11) NOT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_aktivasi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_membership`),
  KEY `id_member` (`id_member`),
  CONSTRAINT `membership_ibfk_1` FOREIGN KEY (`id_member`) 
    REFERENCES `member` (`id_member`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: payment_proof (Bukti Pembayaran)
-- =====================================================
-- Catatan: Tidak ada kolom paket karena sistem menggunakan Premium Lifetime
CREATE TABLE IF NOT EXISTS `payment_proof` (
  `id_payment` INT(11) NOT NULL AUTO_INCREMENT,
  `id_member` INT(11) NOT NULL,
  `file_bukti` VARCHAR(255) NOT NULL,
  `status_verifikasi` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `alasan_penolakan` TEXT DEFAULT NULL,
  `diverifikasi_oleh` INT(11) DEFAULT NULL,
  `diverifikasi_pada` TIMESTAMP NULL DEFAULT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_payment`),
  KEY `id_member` (`id_member`),
  KEY `diverifikasi_oleh` (`diverifikasi_oleh`),
  CONSTRAINT `payment_proof_ibfk_1` FOREIGN KEY (`id_member`) 
    REFERENCES `member` (`id_member`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  CONSTRAINT `payment_proof_ibfk_2` FOREIGN KEY (`diverifikasi_oleh`) 
    REFERENCES `pengguna` (`id_pengguna`) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: notifikasi_member (Log Notifikasi)
-- =====================================================
CREATE TABLE IF NOT EXISTS `notifikasi_member` (
  `id_notifikasi` INT(11) NOT NULL AUTO_INCREMENT,
  `id_member` INT(11) NOT NULL,
  `jenis` ENUM('registrasi', 'approval', 'rejection', 'reminder', 'other') DEFAULT 'other',
  `pesan` TEXT NOT NULL,
  `dikirim_via` ENUM('email', 'whatsapp', 'both') DEFAULT 'email',
  `status_kirim` ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notifikasi`),
  KEY `id_member` (`id_member`),
  CONSTRAINT `notifikasi_member_ibfk_1` FOREIGN KEY (`id_member`) 
    REFERENCES `member` (`id_member`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: live_chat_messages (Live Chat Member & Admin)
-- =====================================================
CREATE TABLE IF NOT EXISTS `live_chat_messages` (
  `id_chat` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pengirim_nama` VARCHAR(80) NOT NULL,
  `pengirim_role` ENUM('member', 'admin') NOT NULL DEFAULT 'member',
  `pesan` TEXT NOT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_chat`),
  KEY `idx_dibuat_pada` (`dibuat_pada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: analisis (Analisis Pasar)
-- =====================================================
CREATE TABLE IF NOT EXISTS `analisis` (
  `id_analisis` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `konten` TEXT NOT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `tanggal` DATE NOT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_analisis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: komentar (Komentar pada Analisis)
-- =====================================================
CREATE TABLE IF NOT EXISTS `komentar` (
  `id_komentar` INT(11) NOT NULL AUTO_INCREMENT,
  `id_analisis` INT(11) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `komentar` TEXT NOT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_komentar`),
  KEY `id_analisis` (`id_analisis`),
  CONSTRAINT `komentar_ibfk_1` FOREIGN KEY (`id_analisis`) 
    REFERENCES `analisis` (`id_analisis`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: portofolio (Portofolio Trading)
-- =====================================================
CREATE TABLE IF NOT EXISTS `portofolio` (
  `id_portofolio` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `tanggal` DATE NOT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_portofolio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: edukasi (Materi Edukasi)
-- =====================================================
CREATE TABLE IF NOT EXISTS `edukasi` (
  `id_edukasi` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `konten` TEXT NOT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `tanggal` DATE NOT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_edukasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: testimoni (Testimoni & Kolaborasi)
-- =====================================================
CREATE TABLE IF NOT EXISTS `testimoni` (
  `id_testimoni` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `testimoni` TEXT NOT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_testimoni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Selesai
-- =====================================================
-- Database schema ARFXTRADE telah dibuat dengan lengkap
-- Semua tabel sudah menggunakan struktur yang benar
-- untuk sistem Premium Lifetime
-- =====================================================
