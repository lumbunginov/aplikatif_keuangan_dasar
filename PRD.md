# PRD: Template Aplikatif — Pencatatan Keuangan Dasar

**Versi:** 1.0
**Tanggal:** 9 Maret 2026
**Author:** Faizal Lazuar
**Status:** Draft
**Base Template:** aplikatif_v1 (TailAdmin Laravel)
**Referensi:** luna2 (Luna AI — versi dengan fitur AI, digunakan sebagai acuan logika bisnis)

---

## 1. Pendahuluan

### 1.1. Visi Produk
Template web app pencatatan keuangan yang bersih, ringan, dan siap pakai — dibangun di atas `aplikatif_v1` (TailAdmin Laravel). Fokus pada fungsi inti: catat transaksi, kelola kategori, pantau saldo & arus kas. Tanpa fitur AI, tanpa kompleksitas berlebihan.

Template ini adalah salah satu produk dari layanan **Aplikatif** — dijual sebagai starter kit atau dikerjakan sebagai jasa untuk klien UMKM, freelancer, dan individu.

### 1.2. Target Pengguna
- **Individu** yang ingin mencatat keuangan pribadi dengan rapi
- **Freelancer** yang perlu melacak pemasukan proyek dan biaya operasional
- **UMKM kecil** yang butuh catatan arus kas sederhana tanpa software akuntansi berat

### 1.3. Masalah yang Diselesaikan
Aplikasi keuangan yang ada di pasaran terlalu rumit (banyak fitur tidak perlu), berbayar mahal, atau tidak bisa dikustomisasi. Template ini memberikan fondasi yang bersih dan bisa dikembangkan sesuai kebutuhan klien.

### 1.4. Perbedaan dengan Luna AI
| Aspek | Luna AI (luna2) | Template Ini |
|---|---|---|
| Stack frontend | Inertia.js + Vue 3 | Blade + Alpine.js (sesuai aplikatif_v1) |
| Fitur AI | Ada (via API) | ❌ Dihapus |
| Auth | PIN + OTP WhatsApp | Email + Password (bawaan aplikatif_v1) |
| Target | SPA mobile-first | Admin dashboard + mobile responsive |
| Distribusi | App standalone | Template/jasa Aplikatif |

---

## 2. Fitur Utama (MVP)

### F-01 — Dashboard Ringkasan
**Prioritas:** Tinggi

Halaman utama yang menampilkan snapshot keuangan bulan berjalan:
- Total **pemasukan** bulan ini
- Total **pengeluaran** bulan ini
- **Saldo bersih** (pemasukan - pengeluaran)
- **Total saldo** dari semua dompet aktif
- **5 transaksi terbaru** (tabel ringkas)
- Filter bulan (dropdown pilih bulan/tahun)

Widget ringkasan menggunakan komponen card dari TailAdmin yang sudah ada.

---

### F-02 — Manajemen Transaksi
**Prioritas:** Tinggi

CRUD penuh untuk transaksi keuangan:

**Data per transaksi:**
| Field | Tipe | Keterangan |
|---|---|---|
| `amount` | decimal | Jumlah uang (wajib) |
| `type` | enum | `income` atau `expense` |
| `category_id` | FK | Kategori transaksi |
| `wallet_id` | FK | Dompet/rekening sumber |
| `transaction_date` | date | Tanggal transaksi (default: hari ini) |
| `description` | string | Keterangan singkat (wajib) |
| `notes` | text | Catatan tambahan (opsional) |

**Fitur tampilan:**
- Tabel dengan filter: tipe (pemasukan/pengeluaran), kategori, dompet, bulan
- Pagination (50 per halaman)
- Badge warna untuk tipe transaksi
- Form tambah/edit via modal atau halaman terpisah
- Konfirmasi sebelum hapus
- Saldo dompet otomatis terupdate saat transaksi ditambah/edit/hapus

---

### F-03 — Manajemen Dompet
**Prioritas:** Tinggi

Kelola dompet/rekening/sumber dana:

**Data per dompet:**
| Field | Tipe | Keterangan |
|---|---|---|
| `name` | string | Nama dompet (mis: "Kas", "BCA", "OVO") |
| `type` | enum | `cash`, `bank`, `ewallet`, `other` |
| `balance` | decimal | Saldo saat ini (auto-update) |
| `description` | string | Keterangan (opsional) |
| `is_active` | boolean | Aktif/nonaktif |

**Fitur:**
- Daftar dompet dengan saldo masing-masing
- Total saldo gabungan semua dompet aktif
- CRUD dompet
- Dompet nonaktif tersembunyi dari pilihan transaksi

---

### F-04 — Manajemen Kategori
**Prioritas:** Tinggi

Kelola kategori transaksi per user:

**Data per kategori:**
| Field | Tipe | Keterangan |
|---|---|---|
| `name` | string | Nama kategori |
| `type` | enum | `income` atau `expense` |
| `color` | string | Warna hex (untuk badge/label) |
| `description` | string | Keterangan (opsional) |
| `is_default` | boolean | Kategori bawaan (tidak bisa dihapus) |

**Kategori default (seeded otomatis):**

*Pemasukan:*
- Penjualan Produk, Penjualan Jasa, Modal Awal, Pendapatan Lainnya

*Pengeluaran:*
- Bahan Baku, Biaya Operasional, Biaya Marketing, Gaji Karyawan, Sewa Tempat, Listrik & Air, Internet & Komunikasi, Transportasi, Pajak, Pengeluaran Lainnya

---

### F-05 — Laporan Sederhana
**Prioritas:** Sedang

Ringkasan keuangan per periode:
- Filter: bulan, atau range tanggal custom
- Tabel rekapitulasi per kategori (total pemasukan & pengeluaran)
- Grafik batang: pemasukan vs pengeluaran per bulan (menggunakan chart component TailAdmin)
- Export ke PDF atau CSV (nice-to-have, bisa di fase 2)

---

### F-06 — Autentikasi & Multi-User
**Prioritas:** Tinggi

Menggunakan sistem auth bawaan `aplikatif_v1`:
- Login / Logout
- Register (bisa dinonaktifkan untuk deployment single-user)
- Forgot password via email
- Semua data terisolasi per user (`user_id` di setiap entitas)
- Role management dari aplikatif_v1 dipertahankan (untuk kebutuhan admin jika multi-tenant)

---

## 3. Yang TIDAK Ada (Sengaja Dihilangkan)

- ❌ Fitur AI / analisis otomatis
- ❌ Integrasi API eksternal (bank, payment gateway)
- ❌ OTP WhatsApp / PIN keamanan tambahan
- ❌ Laporan akuntansi formal (neraca, laba rugi)
- ❌ Multi-mata uang
- ❌ Notifikasi / reminder
- ❌ Aplikasi mobile native

---

## 4. Tech Stack

Mengikuti `aplikatif_v1` sepenuhnya:

| Komponen | Teknologi |
|---|---|
| **Framework** | Laravel 12.x |
| **PHP** | 8.2+ |
| **Database** | MySQL (sesuai WAMP stack Aplikatif) |
| **Frontend** | Blade + Alpine.js |
| **CSS** | Tailwind CSS v4 |
| **Build Tool** | Vite |
| **UI Base** | TailAdmin Laravel |
| **Auth** | Laravel built-in (session-based) |
| **ORM** | Eloquent |

---

## 5. Database Schema

```
users (dari aplikatif_v1)
├── id, name, email, password, ...

wallets
├── id
├── user_id (FK → users)
├── name
├── type (cash|bank|ewallet|other)
├── balance (decimal 15,2)
├── description (nullable)
├── is_active (boolean, default true)
└── timestamps

categories
├── id
├── user_id (FK → users, nullable untuk default global)
├── name
├── type (income|expense)
├── color (hex string, default #6366f1)
├── description (nullable)
├── is_default (boolean, default false)
└── timestamps

transactions
├── id
├── user_id (FK → users)
├── wallet_id (FK → wallets)
├── category_id (FK → categories, nullable)
├── type (income|expense)
├── amount (decimal 15,2)
├── description
├── notes (nullable, text)
├── transaction_date (date)
└── timestamps
```

---

## 6. Struktur Halaman & Routes

```
/                   → redirect ke /dashboard
/login              → halaman login (dari aplikatif_v1)
/register           → halaman register (dari aplikatif_v1)

/dashboard          → Dashboard ringkasan
/transactions       → Daftar transaksi (+ filter)
/transactions/create → Form tambah transaksi
/transactions/{id}/edit → Form edit transaksi
/wallets            → Daftar dompet
/categories         → Daftar kategori
/reports            → Laporan per periode
/settings           → Pengaturan akun (dari aplikatif_v1)
/profile            → Profil user (dari aplikatif_v1)
```

---

## 7. Milestones Pengerjaan

| Fase | Task | Target |
|---|---|---|
| **Setup** | Clone aplikatif_v1, konfigurasi DB, buat migrations | Hari 1 |
| **Model & Seeder** | Model Wallet, Category, Transaction + default seeder | Hari 1 |
| **Auth** | Pastikan auth aplikatif_v1 jalan, data terisolasi per user | Hari 1 |
| **Wallet CRUD** | Controller, routes, views dompet | Hari 2 |
| **Category CRUD** | Controller, routes, views kategori | Hari 2 |
| **Transaction CRUD** | Controller, routes, views transaksi + update saldo otomatis | Hari 2–3 |
| **Dashboard** | Widget ringkasan, tabel transaksi terbaru, filter bulan | Hari 3 |
| **Laporan** | Tabel rekap per kategori + chart | Hari 4 |
| **Polish** | Validasi, error handling, UX detail, seeder demo | Hari 5 |
| **Dokumentasi** | README setup, screenshot | Hari 5 |

**Total estimasi:** 5 hari kerja (bisa lebih cepat dengan AI-assisted coding)

---

## 8. Catatan Pengembangan

- Ikuti konvensi kode dari `aplikatif_v1` (naming, struktur controller, blade components)
- Gunakan komponen UI yang sudah ada di TailAdmin (table, modal, badge, form, chart)
- Saldo dompet **selalu dihitung ulang** saat transaksi create/update/delete (bukan disimpan langsung tanpa validasi)
- Semua query wajib filter `user_id` — jangan sampai data antar user bocor
- Form input amount: gunakan format angka Indonesia (titik sebagai pemisah ribuan)
- Tanggal default: hari ini, tapi bisa diubah
