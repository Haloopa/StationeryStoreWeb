# StationeryStoreWeb
## 📝 Deskripsi
StationeryShop adalah aplikasi e-commerce toko alat tulis berbasis web yang dikembangkan menggunakan framework Laravel. Aplikasi ini menawarkan sistem belanja online lengkap dengan keranjang belanja, checkout via WhatsApp, dan dashboard admin untuk manajemen toko.

---

## ✨ Fitur Utama
### 🛒 E-commerce Features
- Katalog Produk dengan filter kategori, pencarian, dan status stok
- Keranjang Belanja dengan manajemen quantity real-time
- Checkout via WhatsApp - otomatis generate pesan untuk konfirmasi
- Sistem Order dengan status tracking (pending, processing, completed, cancelled)
- Produk Unggulan dengan tampilan responsive

### 👥 User Management
- Registrasi & Login User
- Profil Pengguna dengan update data dan password
- Role System: Admin (full access) & User (customer)
- Riwayat Pesanan per user

### 🛠️ Admin Dashboard
- Dashboard Analytics dengan statistik real-time
- Manajemen Produk (CRUD dengan upload gambar)
- Manajemen Kategori (CRUD dengan slug otomatis)
- Manajemen Pesanan dengan update status
- Manajemen User dengan filter role

---

## 🛠️ Teknologi & Stack
- Backend: Laravel 12
- Frontend: Bootstrap 5 + Bootstrap Icons
- Database: MySQL
- Authentication: Laravel Breeze/Auth
- WhatsApp Integration: WhatsApp API link generator

---

## 📋 Requirement Sistem
- PHP >= 8.3
- Composer
- Node.js & NPM
- Database MySQL
- Web Server (Apache/Nginx) atau PHP built-in server

---

## 🚀 Instalasi & Setup
1. Clone Repository
```bash
git clone https://github.com/Haloopa/StationeryStoreWeb.git
cd StationeryStoreWeb
````

2. Install dependensi backend

```bash
composer install
```

3. Install dependensi frontend

```bash
npm install
```

4. Konfigurasi environment

```bash
cp .env.example .env
php artisan key:generate
```

5. Konfigurasi Database
   Sesuaikan pengaturan database pada file `.env`:

```env
DB_DATABASE=stationery_db
DB_USERNAME=root
DB_PASSWORD=
```

6. Migrasi database & seeder

```bash
php artisan migrate --seed
```

7. Storage Symbolic link

```bash
php artisan storage:link
```

8. Jalankan asset frontend

```bash
npm run dev
```

9. Jalankan server aplikasi

```bash
php artisan serve
```

10. Akses aplikasi melalui:

```
http://127.0.0.1:8000
```

---

## 👤 Akun Default (Seeder)
Akun berikut tersedia secara default melalui database seeder:

| Role   | Email                 | Password |
| ------ | --------------------- | -------- |
| Admin  | admin@stationery.com  | admin123 |
| User   | user@stationery.com   | user123  |

> ⚠️ Disarankan untuk segera mengganti password setelah login pertama.

---

## 📁 Struktur Project
```text
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controller aplikasi
│   │   ├── Middleware/      # Middleware (auth, role, dll)
│   ├── Models/              # Model Eloquent
│   └── Providers/           # Service providers
│
├── bootstrap/               # File bootstrap framework
│
├── config/                  # File konfigurasi aplikasi
│
├── database/
│   ├── migrations/          # File migrasi database
│   ├── seeders/             # Seeder akun default & role
│   └── factories/           # Factory model
│
├── public/
│   └── images/              # Gambar
│
├── resources/
│   ├── css/                 # File CSS aplikasi
│   ├── js/                  # File JavaScript aplikasi
│   └── views/               # Blade templates
│
├── routes/
│   ├── web.php              # Route aplikasi web
│
├── storage/
│   ├── app/                 
│   ├── framework/     
│   └── logs/            
│
├── tests/                   # Unit & feature test
│
├── vendor/                  # Dependensi composer
│
├── .env                     # Konfigurasi environment
├── composer.json            # Konfigurasi Composer
├── package.json             # Konfigurasi NPM
├── vite.config.js           # Konfigurasi Vite
└── README.md                # Dokumentasi project
```

---

## 🔧 Konfigurasi Penting
1. WhatsApp Configuration
   Ubah nomor WhatsApp di OrderController.php:
```php
private function generateWhatsAppMessage($order, $carts, $user)
{
    $phone = "6281234567890"; // Ganti dengan nomor admin
    // ...
}
```

2. File Upload Configuration
- Maksimal ukuran gambar: 2MB
- Format yang didukung: JPEG, PNG, JPG, GIF
- Lokasi penyimpanan: public/images/products/
