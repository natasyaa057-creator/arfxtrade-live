# ARFXTRADE - Platform Personal Branding Trader Profesional

Website platform digital personal branding untuk trader profesional ARFXTRADE, menggunakan Bootstrap 5 (frontend) dan PHP Native (backend).

## 🚀 Fitur Utama

### Halaman Publik
- **Beranda** - Hero banner dengan CTA dan keunggulan
- **Tentang Saya** - Profil lengkap dan timeline karir
- **Portofolio** - Hasil trading dengan filter kategori dan modal detail
- **Analisis Pasar** - Artikel analisis harian dengan pencarian dan pagination
- **Edukasi Trader** - Modul pembelajaran dengan video embed YouTube
- **Testimoni & Kolaborasi** - Form testimoni dan informasi kontak

### Admin Panel
- **Dashboard** - Statistik lengkap dengan Chart.js
- **CRUD Analisis** - Kelola artikel analisis pasar
- **CRUD Portofolio** - Kelola hasil trading
- **CRUD Edukasi** - Kelola materi pembelajaran
- **Kelola Testimoni** - Setujui/hapus testimoni klien

### Fitur Tambahan
- **Dark Mode Toggle** - Tema gelap emas dengan localStorage
- **Responsive Design** - Bootstrap 5 dengan grid system
- **SEO Optimized** - Meta tags dinamis, sitemap.xml, robots.txt
- **Keamanan** - CSRF protection, password hashing, prepared statements
- **Backup Database** - Script otomatis backup MySQL

## 🛠️ Instalasi

### 1. Setup Database
```sql
-- Import file database/db_arfxtrade_complete.sql ke MySQL
mysql -u root -p < database/db_arfxtrade_complete.sql
```

### 2. Konfigurasi Database
Edit file `includes/koneksi.php` sesuai dengan konfigurasi MySQL Anda:
```php
$host_db = 'localhost';
$nama_pengguna_db = 'root';
$kata_sandi_db = '';
$nama_database = 'db-arfxtrade';
```

### 3. Buat Admin Pertama
1. Buka `http://localhost/arfxt/buat_admin.php`
2. Isi form untuk membuat admin pertama
3. **HAPUS file `buat_admin.php` setelah admin dibuat!**

### 4. Akses Website
- **Website**: `http://localhost/arfxt/`
- **Admin**: `http://localhost/arfxt/login.php`

## 📁 Struktur File

```
arfxt/
├── includes/
│   ├── kepala.php          # Header layout
│   ├── kaki.php            # Footer layout
│   ├── koneksi.php         # Database connection
│   ├── fungsi.php          # Utility functions
│   ├── keamanan.php        # CSRF & security
│   └── admin_auth.php      # Admin authentication
├── admin/
│   ├── analisis.php        # CRUD analisis
│   ├── portofolio.php      # CRUD portofolio
│   ├── edukasi.php         # CRUD edukasi
│   └── testimoni.php       # Kelola testimoni
├── aset/
│   ├── css/gaya.css        # Custom styles
│   └── js/
│       ├── tema.js         # Dark mode toggle
│       └── ui.js           # UI interactions
├── database/
│   └── db_arfxtrade.sql    # Database schema
├── index.php               # Beranda
├── tentang.php             # Tentang saya
├── portofolio.php          # Portofolio trading
├── analisis.php            # Analisis pasar
├── edukasi.php             # Edukasi trader
├── testimoni.php           # Testimoni & kolaborasi
├── login.php               # Login admin
├── dashboard.php           # Dashboard admin
├── detail_analisis.php     # Detail analisis
├── backup_db.php           # Backup database
├── sitemap.xml             # SEO sitemap
└── robots.txt              # SEO robots
```

## 🎨 Desain & Tema

- **Warna Utama**: #0a0a0a (hitam pekat)
- **Warna Aksen**: #d4af37 (emas elegan)
- **Font**: Poppins & Montserrat
- **Framework**: Bootstrap 5
- **Animasi**: AOS.js
- **Icons**: FontAwesome 6

## 🔒 Keamanan

- Password hashing dengan `password_hash()`
- CSRF token protection
- Prepared statements untuk SQL injection
- Input sanitization dengan `htmlspecialchars()`
- Session management yang aman

## 📊 Database Schema

### Tabel Utama
- `pengguna` - Data admin dan user
- `portofolio` - Hasil trading
- `analisis` - Artikel analisis pasar
- `edukasi` - Materi pembelajaran
- `testimoni` - Testimoni klien
- `pengunjung` - Log kunjungan
- `komentar` - Komentar analisis

## 🚀 Deployment

1. Upload semua file ke web server
2. Import database schema
3. Konfigurasi koneksi database
4. Buat admin pertama
5. Hapus file `buat_admin.php`
6. Set permissions folder `unggahan/` dan `backup/` ke 755

## 📝 Catatan

- Semua nama file, variabel, dan database menggunakan bahasa Indonesia
- Website sudah responsive untuk mobile dan desktop
- Dark mode toggle tersimpan di localStorage
- Backup database dapat dijalankan via cron job
- SEO optimized dengan meta tags dinamis

## 🤝 Kontribusi

Website ini dibangun khusus untuk ARFXTRADE. Untuk modifikasi atau pengembangan lebih lanjut, silakan hubungi developer.

---

**ARFXTRADE** - Platform Personal Branding Trader Profesional








