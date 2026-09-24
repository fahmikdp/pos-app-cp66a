# Catatan Diagram — POS App
> Dokumen ini berisi catatan lengkap untuk pembuatan diagram laporan akhir semester.

---

## 1. Entity Relationship Diagram (ERD)

### Tabel & Kolom

#### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | Auto increment |
| name | VARCHAR(100) | Nama lengkap |
| email | VARCHAR(255) UNIQUE | Email login |
| password | VARCHAR(255) | Hashed |
| role | ENUM | `superadmin`, `admin`, `kasir` |
| is_active | BOOLEAN | Default: true |
| phone | VARCHAR NULL | Nomor telepon |
| avatar | VARCHAR NULL | Path foto profil |
| created_at | TIMESTAMP | - |
| updated_at | TIMESTAMP | - |

#### `menus`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | - |
| name | VARCHAR UNIQUE | Nama route identifier |
| label | VARCHAR | Label tampil di sidebar |
| route | VARCHAR NULL | Named route Laravel |
| icon | VARCHAR NULL | Heroicons v2 name |
| parent_id | BIGINT FK NULL | Self-referential (parent menu) |
| order | INTEGER | Urutan tampil |
| is_active | BOOLEAN | - |
| permission | VARCHAR NULL | `superadmin`, `admin`, `kasir`, atau NULL |

#### `categories`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | - |
| name | VARCHAR(100) | Nama kategori |
| slug | VARCHAR UNIQUE | Auto-generate dari name |
| description | TEXT NULL | - |
| is_active | BOOLEAN | - |
| created_at | TIMESTAMP | - |
| updated_at | TIMESTAMP | - |

#### `menu_items`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | - |
| category_id | BIGINT FK | → categories.id |
| name | VARCHAR(150) | Nama item menu |
| description | TEXT NULL | - |
| price | DECIMAL(10,2) | Harga satuan |
| stock_qty | INTEGER | Jumlah stok |
| is_available | BOOLEAN | Toggle ketersediaan |
| image | VARCHAR NULL | Path gambar |
| created_at | TIMESTAMP | - |
| updated_at | TIMESTAMP | - |

#### `transactions`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | - |
| user_id | BIGINT FK | → users.id (kasir) |
| invoice_number | VARCHAR UNIQUE | Format: INV-YYYYMMDD-XXXX |
| total_amount | DECIMAL(12,2) | Total belanja |
| payment_amount | DECIMAL(12,2) | Uang yang dibayarkan |
| change_amount | DECIMAL(12,2) | Kembalian |
| status | ENUM | `completed`, `cancelled` |
| notes | TEXT NULL | Catatan transaksi |
| created_at | TIMESTAMP | Waktu transaksi |
| updated_at | TIMESTAMP | - |

#### `transaction_items`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | - |
| transaction_id | BIGINT FK | → transactions.id |
| menu_item_id | BIGINT FK | → menu_items.id |
| item_name | VARCHAR | Snapshot nama item saat transaksi |
| quantity | INTEGER | Jumlah yang dibeli |
| unit_price | DECIMAL(10,2) | Harga per unit saat transaksi |
| subtotal | DECIMAL(12,2) | quantity × unit_price |
| created_at | TIMESTAMP | - |
| updated_at | TIMESTAMP | - |

---

### Relasi Antar Tabel (untuk ERD)

```
users ──────────────── transactions
  1                         *
  (kasir mencatat banyak transaksi)

transactions ─────────── transaction_items
     1                          *
     (satu transaksi punya banyak item)

transaction_items ─────── menu_items
         *                     1
         (banyak item beli satu menu_item)

menu_items ─────────────── categories
    *                          1
    (banyak item masuk satu kategori)

menus ─────────────────── menus (self-referential)
  1 (parent)               * (children)
  (parent menu punya banyak sub-menu)
```

---

## 2. Use Case Diagram

### Aktor
| Aktor | Deskripsi |
|---|---|
| **Superadmin** | Memiliki seluruh akses penuh ke sistem |
| **Admin** | Mengelola menu, kategori, dan melihat laporan |
| **Kasir** | Melakukan transaksi penjualan harian |

### Use Cases per Aktor

#### Superadmin
- Login / Logout
- Melihat Dashboard (data penuh)
- Mengelola Kategori Menu (tambah, edit, hapus)
- Mengelola Item Menu (tambah, edit, hapus, update stok)
- Memproses Transaksi POS
- Mencetak Struk Pembayaran
- Melihat Riwayat Transaksi Semua Kasir
- Melihat Laporan Penjualan (harian & bulanan)
- Mengelola User (tambah, edit, nonaktifkan)
- Melihat Log Error Sistem
- Membersihkan Log Error

#### Admin
- Login / Logout
- Melihat Dashboard
- Mengelola Kategori Menu (tambah, edit, hapus)
- Mengelola Item Menu (tambah, edit, hapus, update stok)
- Melihat Riwayat Semua Transaksi
- Melihat Laporan Penjualan (harian & bulanan)

#### Kasir
- Login / Logout
- Melihat Dashboard (scope data sendiri)
- Memproses Transaksi POS
  - Memilih item menu dari katalog
  - Menambah/kurangi jumlah item
  - Menghitung total otomatis
  - Input nominal pembayaran
  - Hitung kembalian otomatis
- Mencetak Struk Pembayaran (window.print)
- Melihat Riwayat Transaksi Sendiri

---

## 3. Flow Diagram — Proses Transaksi POS

```
[Kasir Login]
     ↓
[Buka Halaman Kasir]
     ↓
[Pilih / Cari Item Menu]  ← filter kategori / search
     ↓
[Klik Item → Masuk Keranjang]
     ↓
[Atur Jumlah Item] (+ / -)
     ↓
[Input Nominal Bayar]
     ↓
[Sistem Hitung Kembalian]
     ↓
[Klik "Proses Pembayaran"]
     ↓
[Sistem Simpan Transaksi ke DB]
[Stok Menu Dikurangi Otomatis]
     ↓
[Modal Struk Muncul]
     ↓
[Kasir Klik "Cetak Struk"] → [window.print() → Printer Thermal 80mm]
     ↓
[Transaksi Selesai, Keranjang Dikosongkan]
```

---

## 4. Diagram Arsitektur Sistem

```
Browser (User)
     │
     ▼
[Laravel Routes] → auth middleware
     │
     ▼
[Livewire Component] (PHP + Alpine.js)
     │
     ├─ Render Blade View
     │
     ├─ Eloquent ORM ──────► [Database SQLite/MySQL]
     │
     └─ WireUI Components (Input, Modal, Select, Button...)
```

---

## 5. Daftar Livewire Components

| Component Class | View | Route | Akses |
|---|---|---|---|
| `Auth\Login` | `livewire/auth/login` | `/login` | Guest |
| `Layout\Sidebar` | `livewire/layout/sidebar` | (embedded) | Auth |
| `Dashboard` | `livewire/dashboard/index` | `/dashboard` | Semua |
| `Manage\Categories` | `livewire/manage/categories` | `/manage/categories` | Admin+ |
| `Manage\MenuItems` | `livewire/manage/menu-items` | `/manage/menu-items` | Admin+ |
| `Pos\Cashier` | `livewire/pos/cashier` | `/pos/cashier` | Kasir+ |
| `Pos\History` | `livewire/pos/history` | `/pos/history` | Kasir+ |
| `Reports\Transactions` | `livewire/reports/transactions` | `/reports/transactions` | Admin+ |
| `Reports\Sales` | `livewire/reports/sales` | `/reports/sales` | Admin+ |
| `Settings\Users` | `livewire/settings/users` | `/settings/users` | Superadmin |
| `Settings\Logs` | `livewire/settings/logs` | `/settings/logs` | Superadmin |

---

## 6. Kamus Data (Data Dictionary)

### Enum: `users.role`
| Nilai | Deskripsi |
|---|---|
| `superadmin` | Akses penuh termasuk manajemen user dan log sistem |
| `admin` | Akses kelola menu, kategori, laporan |
| `kasir` | Akses transaksi dan riwayat sendiri |

### Enum: `transactions.status`
| Nilai | Deskripsi |
|---|---|
| `completed` | Transaksi berhasil diselesaikan |
| `cancelled` | Transaksi dibatalkan |

### Format Invoice Number
```
INV-{YYYYMMDD}-{SEQUENCE}
Contoh: INV-20260922-0001
```

---

## 7. Catatan Teknis untuk Laporan

### Teknologi Utama
- **Laravel 13**: Framework PHP, routing, ORM Eloquent, seeding, migrations
- **Livewire 4**: Komponen reaktif server-side tanpa menulis JavaScript manual
- **WireUI v2**: Library komponen UI siap pakai (input, modal, select, button, toggle, notifikasi)
- **Tailwind CSS v3**: Utility-first CSS, warna primary merah (identik makanan)
- **Alpine.js**: JavaScript ringan untuk interaksi UI client-side (sidebar toggle, dropdown)
- **ApexCharts**: Library grafik untuk dashboard dan laporan penjualan

### Keputusan Desain
1. **Tidak menggunakan Spatie Permission** — role diimplementasikan sebagai enum sederhana pada tabel `users` dengan helper method `isSuperadmin()`, `isAdmin()`, `isKasir()`
2. **Stok otomatis berkurang** — setiap kali transaksi diproses, `stock_qty` dikurangi otomatis menggunakan database transaction
3. **Snapshot nama item** — kolom `item_name` pada `transaction_items` menyimpan nama item saat transaksi terjadi, sehingga jika item diedit/dihapus, data historis tetap akurat
4. **Cetak struk** — menggunakan `window.print()` dengan CSS `@media print` yang membatasi area cetak ke `#receipt-area` saja, format kertas thermal 80mm
5. **Menu sidebar** — dikelola via tabel `menus` di database dengan kolom `permission` untuk filter akses per role, sama persis dengan pattern di crm-app
