# Dokumentasi Sistem Membership Premium ARFXTRADE

## 📋 Daftar Isi
1. [Overview](#overview)
2. [Struktur Database](#struktur-database)
3. [ERD Database](#erd-database)
4. [API Endpoints](#api-endpoints)
5. [Alur Sistem](#alur-sistem)
6. [Middleware Premium Guard](#middleware-premium-guard)
7. [Notifikasi](#notifikasi)
8. [Instalasi](#instalasi)

## Overview

Sistem membership premium ARFXTRADE adalah sistem berlangganan yang memungkinkan user untuk mengakses konten premium seperti analisa harian, signal trading, chart & indikator, dan download materi eksklusif.

### Fitur Utama:
- ✅ Registrasi self-service dengan upload bukti pembayaran
- ✅ Verifikasi admin untuk approve/reject membership
- ✅ Sistem membership **Premium Lifetime** (sekali bayar, akses seumur hidup)
- ✅ Notifikasi via Email dan WhatsApp
- ✅ Middleware premium guard untuk proteksi halaman

## Struktur Database

### Tabel `member`
Menyimpan data member/user premium.

| Field | Type | Keterangan |
|-------|------|------------|
| id_member | INT | Primary Key, Auto Increment |
| nama_lengkap | VARCHAR(150) | Nama lengkap member |
| username | VARCHAR(100) | Username unik |
| email | VARCHAR(150) | Email unik |
| kata_sandi | VARCHAR(255) | Password hashed |
| nomor_whatsapp | VARCHAR(20) | Nomor WhatsApp |
| status_member | ENUM | 'free', 'pending', 'active', 'expired', 'rejected' |
| dibuat_pada | TIMESTAMP | Timestamp registrasi |
| diupdate_pada | TIMESTAMP | Timestamp update terakhir |

### Tabel `membership`
Menyimpan riwayat membership member (Premium Lifetime).

| Field | Type | Keterangan |
|-------|------|------------|
| id_membership | INT | Primary Key, Auto Increment |
| id_member | INT | Foreign Key ke member |
| tanggal_mulai | DATE | Tanggal mulai membership |
| tanggal_aktivasi | TIMESTAMP | Timestamp aktivasi membership |
| dibuat_pada | TIMESTAMP | Timestamp pembuatan |

**Catatan**: 
- Sistem menggunakan **Premium Lifetime** (sekali bayar, akses seumur hidup)
- Tidak ada kolom `paket` karena hanya premium lifetime
- Tidak ada kolom `tanggal_expired` karena lifetime
- Tidak ada kolom `status` karena lifetime selalu aktif
- Status membership ditentukan dari `status_member` di tabel `member` ('active' = premium aktif)

### Tabel `payment_proof`
Menyimpan bukti pembayaran dari member.

| Field | Type | Keterangan |
|-------|------|------------|
| id_payment | INT | Primary Key, Auto Increment |
| id_member | INT | Foreign Key ke member |
| file_bukti | VARCHAR(255) | Path file bukti pembayaran |
| status_verifikasi | ENUM | 'pending', 'approved', 'rejected' |
| alasan_penolakan | TEXT | Alasan jika ditolak |
| diverifikasi_oleh | INT | Foreign Key ke pengguna (admin) |
| diverifikasi_pada | TIMESTAMP | Timestamp verifikasi |
| dibuat_pada | TIMESTAMP | Timestamp upload |

**Catatan**: 
- Tidak ada kolom `paket` karena sistem menggunakan **Premium Lifetime** (sekali bayar, akses seumur hidup)
- Semua pembayaran adalah untuk paket premium yang sama

### Tabel `notifikasi_member`
Menyimpan log notifikasi yang dikirim ke member.

| Field | Type | Keterangan |
|-------|------|------------|
| id_notifikasi | INT | Primary Key, Auto Increment |
| id_member | INT | Foreign Key ke member |
| jenis | ENUM | Jenis notifikasi |
| pesan | TEXT | Isi pesan notifikasi |
| dikirim_via | ENUM | 'email', 'whatsapp', 'both' |
| status_kirim | ENUM | 'pending', 'sent', 'failed' |
| dibuat_pada | TIMESTAMP | Timestamp notifikasi |

## ERD Database

```
┌─────────────┐
│   member    │
├─────────────┤
│ id_member   │◄──┐
│ nama_lengkap│   │
│ username    │   │
│ email       │   │
│ kata_sandi  │   │
│ nomor_wa    │   │
│ status      │   │
└─────────────┘   │
                  │
┌─────────────┐   │
│ membership  │   │
├─────────────┤   │
│ id_membership│  │
│ id_member   │───┘
│ tgl_mulai   │
│ tgl_aktivasi│
└─────────────┘

┌─────────────┐
│payment_proof│
├─────────────┤
│ id_payment  │
│ id_member   │───┐
│ paket       │   │
│ file_bukti  │   │
│ status      │   │
│ alasan      │   │
│ verif_by    │   │
└─────────────┘   │
                  │
┌─────────────┐   │
│notifikasi   │   │
├─────────────┤   │
│ id_notif    │   │
│ id_member   │───┘
│ jenis       │
│ pesan       │
│ dikirim_via │
│ status      │
└─────────────┘
```

## API Endpoints

### Public Endpoints

#### 1. Registrasi Member
- **URL**: `/member_register.php`
- **Method**: POST
- **Body**:
  ```json
  {
    "nama_lengkap": "John Doe",
    "username": "johndoe",
    "email": "john@example.com",
    "kata_sandi": "password123",
    "kata_sandi_konfirmasi": "password123",
    "nomor_whatsapp": "081234567890",
    "paket": "bulanan",
    "bukti_pembayaran": "file"
  }
  ```
- **Response**: Redirect ke `member_register_success.php`

#### 2. Login Member
- **URL**: `/member_login.php`
- **Method**: POST
- **Body**:
  ```json
  {
    "email": "john@example.com",
    "kata_sandi": "password123"
  }
  ```
- **Response**: Set session dan redirect ke dashboard

### Admin Endpoints

#### 3. Verifikasi Membership (Approve)
- **URL**: `/admin/membership_verification.php`
- **Method**: POST
- **Body**:
  ```json
  {
    "action": "approve",
    "id_member": 1,
    "id_payment": 1,
    "tanggal_expired": "2024-12-31" // optional
  }
  ```

#### 4. Verifikasi Membership (Reject)
- **URL**: `/admin/membership_verification.php`
- **Method**: POST
- **Body**:
  ```json
  {
    "action": "reject",
    "id_member": 1,
    "id_payment": 1,
    "alasan_penolakan": "Bukti pembayaran tidak jelas"
  }
  ```

### Premium Endpoints (Protected)

#### 5. Dashboard Member
- **URL**: `/member_dashboard.php`
- **Method**: GET
- **Auth**: Premium Member (Active)
- **Response**: Dashboard dengan menu premium

#### 6. Analisa Harian
- **URL**: `/analisis_harian.php`
- **Method**: GET
- **Auth**: Premium Member (Active)

#### 7. Signal Trading
- **URL**: `/signal_trading.php`
- **Method**: GET
- **Auth**: Premium Member (Active)

#### 8. Chart & Indikator
- **URL**: `/chart_indikator.php`
- **Method**: GET
- **Auth**: Premium Member (Active)

#### 9. Download Premium
- **URL**: `/download_premium.php`
- **Method**: GET
- **Auth**: Premium Member (Active)

## Alur Sistem

### 1. Alur Registrasi
```
User → Form Registrasi → Upload Bukti Pembayaran
  → Status: PENDING → Menunggu Verifikasi Admin
  → Admin Verifikasi → Status: ACTIVE atau REJECTED
  → Notifikasi ke User
```

### 2. Alur Status Membership
```
REGISTRASI → PENDING → ACTIVE (LIFETIME)
                ↓
            REJECTED
```

**Catatan**: Premium Lifetime tidak memiliki expired date, jadi tidak ada status EXPIRED.

### 3. Alur Akses Premium
```
User Login → Cek Status Member (harus 'active') → Cek Membership (harus ada)
  → Jika Valid → Akses Premium (LIFETIME)
  → Jika Tidak Valid → Redirect ke Beranda
```

**Catatan**: Premium Lifetime tidak perlu cek tanggal expired karena akses seumur hidup.

### 4. Alur Notifikasi
```
Verifikasi Berhasil → Email + WhatsApp
Verifikasi Ditolak → Email + WhatsApp (dengan alasan)
Masa Aktif Hampir Habis (3 hari) → Email + WhatsApp
Masa Aktif Habis → Email + WhatsApp → Auto Logout
```

## Middleware Premium Guard

### File: `includes/premium_guard.php`

#### Fungsi `premiumOnly()`
Middleware untuk proteksi halaman premium. Fungsi ini:
1. Cek apakah user sudah login sebagai member
2. Cek status member (harus 'active')
3. Cek membership aktif
4. Cek tanggal expired
5. Jika expired, update status dan logout
6. Cek masa aktif hampir habis (3 hari) untuk notifikasi

#### Penggunaan:
```php
<?php
require_once __DIR__ . '/includes/premium_guard.php';
premiumOnly(); // Proteksi halaman
?>
```

#### Fungsi Helper:
- `isPremiumMember()`: Return true jika member premium aktif
- `getMemberInfo()`: Get informasi member saat ini

## Notifikasi

### File: `includes/notifikasi.php`

### Jenis Notifikasi:
1. **Verifikasi Berhasil**: Dikirim saat admin approve membership
2. **Verifikasi Ditolak**: Dikirim saat admin reject dengan alasan

**Catatan**: 
- Premium Lifetime tidak memiliki expired date, jadi tidak ada notifikasi "Masa Aktif Hampir Habis" atau "Masa Aktif Habis"
- Fungsi notifikasi expired tetap ada di kode untuk kompatibilitas, tapi tidak digunakan

### Konfigurasi:
Edit konstanta di `includes/notifikasi.php`:
- `WHATSAPP_API_URL`: URL API WhatsApp
- `WHATSAPP_API_KEY`: API Key WhatsApp
- `SMTP_*`: Konfigurasi SMTP untuk email

## Instalasi

### 1. Import Database Schema
```bash
mysql -u root -p "db-arfxtrade" < database/db_arfxtrade_complete.sql
```

### 2. Buat Folder Upload
```bash
mkdir -p unggahan/payment_proof
chmod 755 unggahan/payment_proof
```

### 3. Konfigurasi Notifikasi
Edit file `includes/notifikasi.php` dan sesuaikan:
- Konfigurasi WhatsApp API
- Konfigurasi SMTP Email

### 4. Setup Cron Job
**Catatan**: Sistem membership sekarang menggunakan model **Premium Lifetime** (sekali bayar, akses seumur hidup), sehingga tidak diperlukan cron job untuk check expired membership.

### 5. Update Halaman yang Perlu Proteksi
Tambahkan di awal file PHP yang perlu proteksi premium:
```php
<?php
require_once __DIR__ . '/includes/premium_guard.php';
premiumOnly();
?>
```

## Struktur Folder

```
arfxt/
├── admin/
│   └── membership_verification.php  # Dashboard verifikasi admin
├── includes/
│   ├── premium_guard.php            # Middleware premium
│   └── notifikasi.php               # Sistem notifikasi
├── unggahan/
│   └── payment_proof/               # Folder upload bukti pembayaran
├── database/
│   └── membership_schema.sql        # Schema database membership
├── member_register.php              # Halaman registrasi
├── member_register_success.php       # Halaman sukses registrasi
├── member_logout.php                # Logout member
├── member_dashboard.php             # Dashboard member premium
├── analisis_harian.php              # Analisa harian (premium)
├── signal_trading.php               # Signal trading (premium)
├── chart_indikator.php              # Chart & indikator (premium)
└── download_premium.php             # Download premium (premium)
```

## Testing

### Test Case 1: Registrasi
1. Buka `/member_register.php`
2. Isi form registrasi
3. Upload bukti pembayaran
4. Submit → Harus redirect ke success page
5. Cek database → Status harus 'pending'

### Test Case 2: Verifikasi Admin
1. Login sebagai admin
2. Buka `/admin/membership_verification.php`
3. Approve member pending
4. Cek database → Status harus 'active', membership harus dibuat
5. Cek notifikasi → Harus terkirim

### Test Case 3: Akses Premium
1. Login sebagai member aktif
2. Akses `/analisis_harian.php` → Harus bisa akses
3. Logout
4. Login sebagai member expired → Harus auto logout

### Test Case 4: Lifetime Membership
1. Verifikasi member → Status harus 'active'
2. Membership harus bertipe 'premium' dengan lifetime access
3. Tidak ada expired date (akses seumur hidup)

## Troubleshooting

### Issue: Member tidak bisa login setelah verifikasi
**Solusi**: Cek session dan pastikan `member_id` sudah di-set di session.

### Issue: Notifikasi tidak terkirim
**Solusi**: 
- Cek konfigurasi WhatsApp API dan SMTP
- Cek log error PHP
- Pastikan fungsi `kirim_email()` dan `kirim_whatsapp()` berfungsi

### Issue: Premium guard selalu redirect
**Solusi**: 
- Cek status member di database
- Cek tanggal expired membership
- Pastikan membership status = 'active'

## Support

Untuk pertanyaan atau issue, silakan hubungi tim development ARFXTRADE.





