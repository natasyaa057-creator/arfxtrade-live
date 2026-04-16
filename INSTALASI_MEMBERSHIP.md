# Panduan Instalasi Sistem Membership Premium ARFXTRADE

## 📋 Prasyarat

1. **Server Requirements**:
   - PHP 7.4 atau lebih tinggi
   - MySQL 5.7 atau lebih tinggi
   - Apache/Nginx web server
   - Extension PHP: `mysqli`, `gd` (untuk image processing)

2. **Software**:
   - XAMPP/WAMP/LAMP (untuk development)
   - Text editor (VS Code, PHPStorm, dll)

## 🚀 Langkah Instalasi

### 1. Import Database Schema

Jalankan file SQL untuk membuat tabel membership:

```bash
# Via MySQL Command Line
mysql -u root -p "db-arfxtrade" < database/db_arfxtrade_complete.sql

# Atau via phpMyAdmin
# 1. Buka phpMyAdmin
# 2. Pilih database db-arfxtrade
# 3. Klik tab "Import"
# 4. Pilih file database/db_arfxtrade_complete.sql
# 5. Klik "Go"
```

**Tabel yang akan dibuat**:
- `member` - Data member/user premium
- `membership` - Riwayat langganan
- `payment_proof` - Bukti pembayaran
- `notifikasi_member` - Log notifikasi

### 2. Buat Folder Upload

Buat folder untuk menyimpan bukti pembayaran:

**Windows (PowerShell)**:
```powershell
if (-not (Test-Path "unggahan\payment_proof")) { 
    New-Item -ItemType Directory -Path "unggahan\payment_proof" -Force 
}
```

**Linux/Mac**:
```bash
mkdir -p unggahan/payment_proof
chmod 755 unggahan/payment_proof
```

**Atau manual**:
- Buat folder `unggahan/payment_proof` di root project
- Set permission: 755 (read, write, execute untuk owner)

### 3. Konfigurasi Database

Pastikan file `includes/koneksi.php` sudah dikonfigurasi dengan benar:

```php
$host_db = 'localhost';
$nama_pengguna_db = 'root';
$kata_sandi_db = ''; // Sesuaikan dengan password MySQL Anda
$nama_database = 'db-arfxtrade';
```

### 4. Konfigurasi Notifikasi

Edit file `includes/notifikasi.php` dan sesuaikan konfigurasi:

#### Email (SMTP)
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@arfxtrade.com');
define('SMTP_FROM_NAME', 'ARFXTRADE');
```

**Catatan**: Untuk Gmail, gunakan App Password, bukan password biasa.

#### WhatsApp API
```php
define('WHATSAPP_API_URL', 'https://api.whatsapp.com/send');
define('WHATSAPP_API_KEY', 'your-api-key'); // Jika menggunakan API service
```

**Opsi WhatsApp**:
1. **WhatsApp Business API** (Resmi) - Perlu verifikasi bisnis
2. **Twilio WhatsApp API** - Berbayar, mudah setup
3. **WhatsApp Web Link** - Gratis, hanya link (default)

### 5. Setup Cron Job (Optional)

Untuk auto-check expired membership setiap hari:

**Windows (Task Scheduler)**:
1. Buka Task Scheduler
2. Create Basic Task
**Catatan**: Sistem membership sekarang menggunakan model **Premium Lifetime** (sekali bayar, akses seumur hidup), sehingga tidak diperlukan cron job untuk check expired membership.

### 6. Test Instalasi

#### Test 1: Database Connection
Buka browser dan akses:
```
http://localhost/arfxt/member_register.php
```
Jika halaman muncul tanpa error, database connection OK.

#### Test 2: Upload Folder
Coba registrasi member baru dan upload bukti pembayaran. Cek apakah file tersimpan di `unggahan/payment_proof/`.

#### Test 3: Admin Verification
1. Login sebagai admin
2. Buka `/admin/membership_verification.php`
3. Cek apakah list pending member muncul

#### Test 4: Premium Access
1. Registrasi member baru
2. Admin approve membership
3. Login sebagai member
4. Akses `/analisis_harian.php` - harus bisa akses

## 🔧 Troubleshooting

### Error: "Koneksi gagal"
**Solusi**: 
- Cek konfigurasi database di `includes/koneksi.php`
- Pastikan MySQL service running
- Cek username dan password MySQL

### Error: "Failed to upload file"
**Solusi**:
- Cek permission folder `unggahan/payment_proof` (harus 755 atau 777)
- Cek `upload_max_filesize` di php.ini (minimal 5MB)
- Cek `post_max_size` di php.ini

### Error: "Notifikasi tidak terkirim"
**Solusi**:
- Cek konfigurasi SMTP di `includes/notifikasi.php`
- Untuk Gmail, pastikan menggunakan App Password
- Test fungsi `kirim_email()` secara manual
- Cek log error PHP

### Error: "Premium guard selalu redirect"
**Solusi**:
- Cek status member di database (harus 'active')
- Cek membership status (harus 'active')
- Cek tanggal expired (harus belum lewat)
- Pastikan session member_id sudah di-set

### Error: "CSRF token tidak valid"
**Solusi**:
- Pastikan session sudah start
- Cek apakah form menggunakan `token_csrf()`
- Clear browser cache dan cookies

## 📁 Struktur File yang Dibuat

```
arfxt/
├── admin/
│   └── membership_verification.php    # Dashboard verifikasi admin
├── includes/
│   ├── premium_guard.php              # Middleware premium
│   └── notifikasi.php                 # Sistem notifikasi
├── unggahan/
│   └── payment_proof/                  # Folder upload bukti pembayaran
├── database/
│   └── membership_schema.sql          # Schema database
├── member_register.php                # Halaman registrasi
├── member_register_success.php         # Halaman sukses
├── member_logout.php                  # Logout member
├── member_dashboard.php               # Dashboard member
├── analisis_harian.php                # Analisa harian (premium)
├── signal_trading.php                 # Signal trading (premium)
├── chart_indikator.php                # Chart & indikator (premium)
├── download_premium.php               # Download premium (premium)
├── MEMBERSHIP_SYSTEM_DOCUMENTATION.md  # Dokumentasi lengkap
└── INSTALASI_MEMBERSHIP.md            # File ini
```

## 🔐 Keamanan

### Rekomendasi untuk Production:

1. **Password Hashing**: Sudah menggunakan `password_hash()` dengan bcrypt
2. **CSRF Protection**: Semua form menggunakan CSRF token
3. **SQL Injection**: Menggunakan prepared statements
4. **XSS Protection**: Output di-escape dengan `htmlspecialchars()`
5. **File Upload**: 
   - Validasi extension file
   - Validasi size file
   - Rename file dengan uniqid
6. **Session Security**:
   - Set `session.cookie_httponly = 1` di php.ini
   - Set `session.cookie_secure = 1` untuk HTTPS
7. **Folder Upload**:
   - Jangan set permission 777 di production
   - Gunakan .htaccess untuk proteksi direktori
   - Simpan file di luar web root jika memungkinkan

### File .htaccess untuk Proteksi Upload

Buat file `.htaccess` di folder `unggahan/payment_proof/`:

```apache
# Deny direct access
<FilesMatch "\.(jpg|jpeg|png|pdf)$">
    Order Allow,Deny
    Allow from 127.0.0.1
    Deny from all
</FilesMatch>
```

Atau lebih baik, simpan file di luar web root dan akses via PHP script.

## 📞 Support

Jika mengalami masalah:

1. Cek dokumentasi di `MEMBERSHIP_SYSTEM_DOCUMENTATION.md`
2. Cek dokumentasi sistem di `MEMBERSHIP_SYSTEM_DOCUMENTATION.md`
3. Cek log error PHP
4. Cek log error MySQL
5. Hubungi tim development ARFXTRADE

## ✅ Checklist Instalasi

- [ ] Database schema diimport
- [ ] Folder `unggahan/payment_proof` dibuat dengan permission yang benar
- [ ] Konfigurasi database di `includes/koneksi.php` sudah benar
- [ ] Konfigurasi notifikasi di `includes/notifikasi.php` sudah diset
- [ ] Cron job untuk check expired sudah disetup (optional)
- [ ] Test registrasi member berhasil
- [ ] Test upload bukti pembayaran berhasil
- [ ] Test verifikasi admin berhasil
- [ ] Test login member berhasil
- [ ] Test akses premium berhasil
- [ ] Notifikasi email/WhatsApp berfungsi

## 🎉 Selesai!

Setelah semua checklist terpenuhi, sistem membership premium siap digunakan!

**Next Steps**:
1. Buat akun admin jika belum ada
2. Test registrasi member pertama
3. Verifikasi member via admin dashboard
4. Test akses premium
5. Setup notifikasi email/WhatsApp
6. Monitor expired membership via cron job





