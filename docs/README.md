# POS App — Dokumentasi Proyek

## Deskripsi

**POS App** adalah aplikasi Point of Sale (Sistem Kasir Digital) berbasis web yang dibangun menggunakan Laravel 13 + Livewire 4. Aplikasi ini dirancang untuk memudahkan proses transaksi penjualan di warung/restoran sederhana, dilengkapi dengan manajemen menu, laporan penjualan, dan manajemen pengguna.

---

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 13.x (PHP 8.3) |
| Frontend Reaktif | Livewire 4.x + Alpine.js |
| UI Component Library | WireUI v2 (`wireui-` prefix) |
| Icons | Heroicons v2 |
| CSS Framework | Tailwind CSS v3 (darkMode: class) |
| Charts | ApexCharts JS |
| Database | MySQL |
| Build Tool | Vite 5 |

---

## Instalasi

```bash
# Clone / buat project
cd C:\codingFiles\college\pos-app

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file
cp .env.example .env
php artisan key:generate

# Jalankan migrasi + seeding
php artisan migrate:fresh --seed

# Build aset frontend
npm run build

# Jalankan server
php artisan serve
```

---

## Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Superadmin | superadmin@pos.test | password |
| Admin | admin@pos.test | password |
| Kasir 1 | kasir1@pos.test | password |
| Kasir 2 | kasir2@pos.test | password |

---

## Struktur Direktori Penting

```
pos-app/
├── app/
│   ├── Livewire/
│   │   ├── Auth/Login.php
│   │   ├── Layout/Sidebar.php
│   │   ├── Dashboard.php
│   │   ├── Manage/
│   │   │   ├── Categories.php
│   │   │   └── MenuItems.php
│   │   ├── Pos/
│   │   │   ├── Cashier.php
│   │   │   └── History.php
│   │   ├── Reports/
│   │   │   ├── Transactions.php
│   │   │   └── Sales.php
│   │   └── Settings/
│   │       ├── Users.php
│   │       └── Logs.php
│   └── Models/
│       ├── User.php
│       ├── Menu.php
│       ├── Category.php
│       ├── MenuItem.php
│       ├── Transaction.php
│       └── TransactionItem.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── guest.blade.php
│       └── livewire/
│           ├── auth/
│           ├── layout/
│           ├── dashboard/
│           ├── manage/
│           ├── pos/
│           ├── reports/
│           └── settings/
├── routes/web.php
├── tailwind.config.js
└── docs/
    ├── README.md
    └── DIAGRAM_NOTES.md
```

---

## Konfigurasi Tema

Primary color dapat diubah di `tailwind.config.js`:

```js
primary: colors.red,    // merah (default)
// primary: colors.rose,
// primary: colors.orange,
```

---

## Fitur & Hak Akses

| Fitur | Superadmin | Admin | Kasir |
|---|:---:|:---:|:---:|
| Dashboard (ringkasan data) | ✅ | ✅ | ✅ |
| Kelola Kategori (CRUD) | ✅ | ✅ | ❌ |
| Kelola Item Menu (CRUD + Stok) | ✅ | ✅ | ❌ |
| Transaksi POS (Kasir) | ✅ | ❌ | ✅ |
| Cetak Struk (window.print) | ✅ | ❌ | ✅ |
| Riwayat Transaksi Sendiri | ✅ | ❌ | ✅ |
| Riwayat Semua Transaksi | ✅ | ✅ | ❌ |
| Laporan Penjualan (grafik) | ✅ | ✅ | ❌ |
| Manajemen User (CRUD) | ✅ | ❌ | ❌ |
| Log Error Sistem | ✅ | ❌ | ❌ |

---

## Fitur Cetak Struk

Fitur cetak struk menggunakan `window.print()` native browser — **tidak memerlukan package tambahan**.

### Cara Kerja
1. Setelah transaksi berhasil diproses, modal struk muncul otomatis.
2. Kasir klik tombol **Cetak Struk** (ikon printer) di footer modal.
3. Browser membuka dialog print. Pilih printer thermal atau simpan sebagai PDF.

### Implementasi
- **Tombol cetak**: `resources/views/livewire/pos/cashier.blade.php` — footer `<x-wireui-modal>`, memanggil `onclick="window.print()"`.
- **Area cetak**: `<div id="receipt-area">` di dalam modal yang sama.
- **CSS print**: `resources/css/app.css` — `@media print` menyembunyikan seluruh halaman kecuali `#receipt-area`, dengan ukuran kertas `80mm auto` (thermal 80mm).

### Format Struk
```
POS APP
Sistem Kasir Digital
dd/mm/yyyy HH:mm
Kasir: [nama kasir]
INV-YYYYMMDD-XXXX
--------------------------------
[nama item]
[qty] x Rp [harga]          Rp [subtotal]
--------------------------------
TOTAL                        Rp [total]
Bayar                        Rp [bayar]
Kembalian                    Rp [kembalian]
--------------------------------
Terima kasih atas kunjungan Anda!
```

---

## Perintah Berguna

```bash
# Reset dan seed ulang database
php artisan migrate:fresh --seed

# Format kode PHP (Laravel Pint)
vendor/bin/pint

# Build frontend
npm run build

# Clear view cache
php artisan view:clear
```
