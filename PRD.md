# PRD — Aplikatif Base Admin Template v1.0

**Dibuat:** 2026-03-07
**Diperbarui:** 2026-03-07 (tambah PWA)
**Status:** Draft
**Author:** Faizal Lazuar (Aplikatif)

---

## Overview

Template dasar Laravel yang digunakan sebagai fondasi untuk semua proyek klien Aplikatif. Dirancang untuk bisa di-clone dan dikustomisasi per klien dalam waktu < 30 menit.

### Stack
- **Backend:** Laravel 12
- **Frontend:** TailAdmin (Tailwind CSS v4 + Alpine.js)
- **Build tool:** Vite
- **Database:** MySQL
- **Session/Cache:** File (default) / Redis (opsional)

### Goals
- Satu base template reusable untuk semua klien
- Auth, role, email, dan activity log sudah siap tanpa setup ulang
- Developer bisa fokus ke fitur bisnis klien, bukan boilerplate

---

## 1. Arsitektur Folder

```
aplikatif-base/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                  ← Login, Register, Forgot/Reset Password
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── RoleController.php
│   │   │   └── SettingController.php
│   │   └── Middleware/
│   │       └── CheckPermission.php
│   ├── Models/
│   │   ├── User.php
│   │   └── Setting.php
│   └── Notifications/
│       ├── ResetPasswordNotification.php
│       └── WelcomeNotification.php
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_users_table.php
│   │   ├── xxxx_create_settings_table.php
│   │   └── xxxx_create_activity_log_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminSeeder.php
│       └── RolePermissionSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php          ← Layout utama (sidebar + navbar)
│   │   │   └── auth.blade.php         ← Layout halaman auth
│   │   ├── components/
│   │   │   ├── alert.blade.php
│   │   │   ├── modal-confirm.blade.php
│   │   │   ├── pagination.blade.php
│   │   │   └── stat-card.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   └── reset-password.blade.php
│   │   ├── dashboard/
│   │   │   └── index.blade.php
│   │   ├── users/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── edit.blade.php
│   │   ├── roles/
│   │   │   ├── index.blade.php
│   │   │   └── edit.blade.php
│   │   ├── settings/
│   │   │   └── index.blade.php
│   │   ├── activity-log/
│   │   │   └── index.blade.php
│   │   └── emails/
│   │       ├── reset-password.blade.php
│   │       ├── welcome.blade.php
│   │       └── notification.blade.php
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php
│   └── auth.php
└── config/
    └── app-settings.php               ← Konfigurasi per klien
```

---

## 2. Modul & Fitur

### 2.1 Auth System

**Halaman:**
- Login (email + password)
- Forgot Password (form kirim email)
- Reset Password (form set password baru)
- Logout

**Behavior:**
- Remember Me → simpan session 30 hari
- Rate limiting → max 5x login gagal → cooldown 1 menit
- Session timeout → konfigurabel via `.env` (default: 120 menit)
- Redirect after login → `/dashboard`
- Redirect setelah logout → `/login`

**Tidak ada:**
- ❌ Register publik (user dibuat oleh admin)
- ❌ Social login (Google, GitHub)
- ❌ 2FA (v2.0)

---

### 2.2 User Management

**Halaman:**
- `/users` → List semua user (tabel + search + filter status)
- `/users/create` → Form tambah user
- `/users/{id}/edit` → Form edit user

**Fields User:**
| Field | Type | Keterangan |
|-------|------|------------|
| name | string | Nama lengkap |
| email | string | Unique, untuk login |
| password | hashed | Min 8 karakter |
| role | relation | Via Spatie Permission |
| status | enum | active / inactive |
| photo | string | Path ke storage |
| created_at | timestamp | Auto |

**Behavior:**
- Admin tidak bisa hapus atau nonaktifkan dirinya sendiri
- Ganti password tidak perlu password lama (admin reset)
- Upload foto profil → resize 200x200px → simpan di `storage/app/public/photos`
- Inactive user tidak bisa login

**Permission required:**
- `view users` → lihat list
- `create users` → tambah user
- `edit users` → edit user
- `delete users` → hapus user

---

### 2.3 Role & Permission Management

**Roles default (seeder):**
| Role | Deskripsi |
|------|-----------|
| superadmin | Akses penuh semua fitur |
| admin | Kelola user & data, tidak bisa ubah settings sistem |
| staff | Hanya akses fitur operasional (dikustomisasi per klien) |

**Halaman:**
- `/roles` → List roles + jumlah user per role
- `/roles/{id}/edit` → Assign permission ke role

**Packages:** `spatie/laravel-permission`

---

### 2.4 App Settings

**Halaman:** `/settings`

**Konfigurasi yang bisa diubah dari UI:**
| Key | Deskripsi | Type |
|-----|-----------|------|
| app_name | Nama aplikasi | text |
| app_logo | Logo (upload) | file |
| app_favicon | Favicon (upload) | file |
| primary_color | Warna tema utama | color picker |
| timezone | Timezone | select |
| mail_from_name | Nama pengirim email | text |
| mail_from_address | Email pengirim | email |
| maintenance_mode | Toggle maintenance | boolean |
| session_lifetime | Durasi session (menit) | number |

**Behavior:**
- Disimpan di tabel `settings` (key-value)
- Cache settings → invalidate saat ada perubahan
- Hanya `superadmin` yang bisa mengakses halaman ini

---

### 2.5 Email System

**Driver:** SMTP (konfigurasi via `.env`)

**Template email yang tersedia:**
1. **Reset Password** → Link reset + instruksi
2. **Welcome User Baru** → Dikirim saat admin tambah user baru (opsional, toggle)
3. **Notifikasi Sistem** → Template generik untuk notifikasi custom per klien

**Semua template email:**
- Branded dengan logo & nama aplikasi dari settings
- Responsive (mobile-friendly)
- Plain-text fallback

**Konfigurasi .env:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@aplikatif.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Fitur test email:**
- Tombol "Kirim Test Email" di halaman Settings
- Kirim ke email superadmin yang sedang login

---

### 2.6 Activity Log

**Halaman:** `/activity-log`

**Apa yang dicatat:**
| Event | Contoh |
|-------|--------|
| Auth | User login, logout, gagal login |
| User | Tambah user, edit user, hapus user |
| Settings | Ubah setting |
| Custom | Bisa ditambahkan per proyek klien |

**Tabel tampilan:**
- Kolom: Waktu, User, Aksi, Subject, Detail
- Filter: by user, by tanggal, by tipe aksi
- Pagination: 25 per halaman
- Tidak bisa diedit/dihapus (read-only)

**Package:** `spatie/laravel-activitylog`

---

### 2.7 Dashboard

**Halaman:** `/dashboard`

**Konten default:**
- Greeting: "Selamat pagi/siang/sore, {nama user}"
- Stat cards: placeholder (dikustomisasi per klien)
- Area chart: ApexCharts, data dummy
- Recent activity: 10 log aktivitas terakhir

**Catatan:** Dashboard adalah halaman paling sering dikustomisasi per klien. Buat modular agar mudah diganti.

---

## 3. Database Schema

### Tabel Core

```sql
-- users
CREATE TABLE users (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(255) NOT NULL,
  email         VARCHAR(255) UNIQUE NOT NULL,
  password      VARCHAR(255) NOT NULL,
  status        ENUM('active','inactive') DEFAULT 'active',
  photo         VARCHAR(255) NULL,
  remember_token VARCHAR(100) NULL,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL
);

-- settings (key-value)
CREATE TABLE settings (
  id    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  key   VARCHAR(255) UNIQUE NOT NULL,
  value TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

-- Spatie Permission Tables (auto-generated)
-- roles, permissions, model_has_roles, model_has_permissions, role_has_permissions

-- Activity Log (auto-generated by Spatie)
-- activity_log
```

---

## 4. UI/UX Requirements

### Layout
- **Sidebar:** Collapsible, highlight menu aktif sesuai route
- **Navbar:** User avatar + nama, dropdown profil, toggle dark mode
- **Content area:** Padding konsisten, max-width container

### Responsiveness
- **Mobile (< 768px):** Sidebar hidden by default, toggle dari hamburger
- **Tablet (768–1024px):** Sidebar mini (icon only)
- **Desktop (> 1024px):** Sidebar full

### Dark Mode
- Toggle di navbar
- Preferensi disimpan di `localStorage`
- Semua komponen support dark mode

### Feedback & Interaksi
- Toast notification (sukses/error/warning) via Alpine.js
- Loading spinner saat submit form
- Modal konfirmasi sebelum hapus data
- Disabled state pada tombol submit saat loading

### Tabel & Data
- Search input di atas tabel
- Filter dropdown (status, role, dll)
- Pagination dengan info "Showing X-Y of Z results"
- Pilihan items per halaman: 10 / 25 / 50
- Kolom sortable (nama, email, tanggal)
- Empty state illustration saat tidak ada data

---

## 5. Security

| Aspek | Implementasi |
|-------|-------------|
| CSRF | Laravel default (semua POST/PUT/DELETE) |
| XSS | Blade auto-escape `{{ }}` |
| SQL Injection | Eloquent ORM, no raw queries |
| Auth | Laravel Auth middleware |
| Authorization | Spatie Permission middleware |
| Password | Bcrypt hashing |
| Rate limiting | `throttle:5,1` pada route login |
| HTTP Headers | Middleware security headers |
| File upload | Validasi tipe & ukuran, simpan di private storage |

---

## 6. Packages

### PHP (Composer)
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "spatie/laravel-permission": "^6.0",
        "spatie/laravel-activitylog": "^4.0",
        "intervention/image": "^3.0"
    }
}
```

### JavaScript (NPM)
```json
{
    "dependencies": {
        "apexcharts": "^3.x",
        "@alpinejs/focus": "^3.x"
    }
}
```

---

## 7. Environment Configuration

```env
# App
APP_NAME="Aplikatif Base"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aplikatif_base
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@aplikatif.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 8. Seeder & Default Data

### AdminSeeder
```
Email   : admin@aplikatif.com
Password: password
Role    : superadmin
Status  : active
```

### RolePermissionSeeder
```
Roles:
  - superadmin (semua permission)
  - admin (manage users, view activity)
  - staff (custom per klien)

Permissions:
  - view dashboard
  - view users, create users, edit users, delete users
  - view roles, edit roles
  - view settings, edit settings
  - view activity-log
```

### SettingsSeeder
```
app_name       = "Aplikatif Base"
primary_color  = "#4F46E5"
timezone       = "Asia/Jakarta"
session_lifetime = 120
```

---

## 9. Clone Workflow (Untuk Proyek Klien Baru)

```bash
# 1. Clone base template
git clone https://github.com/aplikatif/base.git nama-proyek-klien
cd nama-proyek-klien

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env
# - Sesuaikan DB_DATABASE, APP_NAME, MAIL_*

# 5. Migrate & seed
php artisan migrate --seed

# 6. Build frontend
npm run build

# 7. Jalankan
php artisan serve
```

Setelah langkah di atas, akses `/settings` untuk:
- Upload logo & favicon klien
- Sesuaikan warna tema
- Set email sender

---

## 10. Progressive Web App (PWA)

Template ini harus bisa diinstall sebagai PWA di HP klien maupun staf.

### Kenapa PWA untuk Admin Panel?
- Klien UMKM kebanyakan akses dari HP
- Bisa di-install ke home screen tanpa ke Play Store / App Store
- Offline indicator (tahu kalau internet mati)
- Notifikasi push (opsional, v2.0)
- Feel seperti native app

### Requirements

#### Web App Manifest (`/manifest.json`)
```json
{
  "name": "Aplikatif Admin",
  "short_name": "Aplikatif",
  "description": "Admin panel Aplikatif",
  "start_url": "/dashboard",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#4F46E5",
  "orientation": "portrait-primary",
  "icons": [
    { "src": "/icons/icon-72.png",   "sizes": "72x72",   "type": "image/png" },
    { "src": "/icons/icon-96.png",   "sizes": "96x96",   "type": "image/png" },
    { "src": "/icons/icon-128.png",  "sizes": "128x128", "type": "image/png" },
    { "src": "/icons/icon-144.png",  "sizes": "144x144", "type": "image/png" },
    { "src": "/icons/icon-192.png",  "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png",  "sizes": "512x512", "type": "image/png", "purpose": "any maskable" }
  ]
}
```

**Catatan:** `name`, `short_name`, `theme_color`, dan icons diambil dari App Settings → otomatis update saat admin ubah branding klien.

#### Service Worker
- **Strategy:** Network First (untuk admin panel — data harus selalu fresh)
- **Cache:** Assets statis (CSS, JS, fonts, icons) → Cache First
- **Offline page:** Tampil halaman `/offline` yang informatif saat tidak ada koneksi
- **Auto-update:** SW update otomatis di background, banner "Update tersedia" muncul

#### File yang dibuat
```
public/
├── manifest.json
├── sw.js                    ← Service Worker
├── offline.html             ← Halaman offline fallback
└── icons/
    ├── icon-72.png
    ├── icon-96.png
    ├── icon-128.png
    ├── icon-144.png
    ├── icon-192.png
    └── icon-512.png         ← Maskable (safe zone)
```

#### Integrasi Laravel
```php
// Di layout app.blade.php — head section
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="{{ setting('primary_color', '#4F46E5') }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ setting('app_name') }}">
<link rel="apple-touch-icon" href="/icons/icon-192.png">

// Register SW
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
  }
</script>
```

#### Package
```json
// Gunakan Vite PWA plugin untuk generate SW otomatis
"vite-plugin-pwa": "^0.20.x"
```

Atau bisa manual `sw.js` tanpa plugin (lebih simpel, kontrol penuh).

### PWA Settings di App Settings
Tambahkan field di halaman `/settings`:
| Key | Deskripsi |
|-----|-----------|
| pwa_enabled | Toggle aktif/nonaktif PWA |
| pwa_short_name | Nama singkat di home screen (max 12 karakter) |
| pwa_theme_color | Warna status bar HP saat dibuka sebagai PWA |

### Offline Behavior
- Halaman yang di-cache: `/dashboard`, `/offline`
- Assets statis selalu dari cache → performa optimal
- Halaman data (users, dll) → network first, fallback ke offline page
- Banner kecil muncul saat offline: "Tidak ada koneksi internet"

### Installability Checklist
- [ ] Served over HTTPS (wajib untuk PWA — production)
- [ ] manifest.json valid & terhubung di `<head>`
- [ ] Service Worker terdaftar & aktif
- [ ] Icons 192x192 & 512x512 tersedia
- [ ] `start_url` accessible
- [ ] `display: standalone` di manifest

### Estimasi Tambahan
| Task | Estimasi |
|------|----------|
| Setup manifest.json + icons | 1 jam |
| Service Worker (network/cache first) | 1.5 jam |
| Offline page | 30 menit |
| Integrasi Settings (dynamic manifest) | 1 jam |
| Testing install di Android & iOS | 30 menit |
| **Total** | **~4.5 jam** |

---

## 11. Out of Scope (v1.0)

- ❌ Register publik
- ❌ Social login (Google, GitHub)
- ❌ Two-Factor Authentication (2FA)
- ❌ Multi-tenancy
- ❌ REST API / JSON endpoints
- ❌ Subscription / billing
- ❌ Fitur spesifik per klien (dikerjakan di atas base ini)
- ❌ Multi-language / i18n
- ❌ Push notification PWA (v2.0)

---

## 11. Definition of Done

- [ ] Setup proyek baru berhasil dengan 7 langkah di atas
- [ ] Login, logout, forgot/reset password berfungsi
- [ ] Rate limiting login aktif
- [ ] CRUD user berfungsi (create, edit, delete, nonaktifkan)
- [ ] Role & permission teraplikasi di semua route
- [ ] Email reset password terkirim
- [ ] App settings bisa diubah dari UI dan langsung berlaku
- [ ] Dark mode berfungsi & persisten
- [ ] Semua halaman responsive di mobile (360px+)
- [ ] Activity log mencatat login, logout, CRUD user, ubah settings
- [ ] Seed default berjalan (admin + roles + settings)
- [ ] Tidak ada error di `php artisan route:list`
- [ ] README setup 7 langkah
- [ ] PWA bisa diinstall di Android & iOS
- [ ] Offline page tampil saat tidak ada koneksi
- [ ] manifest.json valid (cek via Lighthouse)

---

## 12. Estimasi Pengerjaan

| Modul | Estimasi |
|-------|----------|
| Setup project + TailAdmin install | 1 jam |
| Layout (sidebar, navbar, dark mode) | 2 jam |
| Auth system (login, forgot, reset) | 2 jam |
| User management (CRUD) | 3 jam |
| Role & permission | 2 jam |
| App settings | 2 jam |
| Activity log | 1 jam |
| Email templates | 1 jam |
| PWA (manifest + SW + offline page) | 4.5 jam |
| Seeder & testing | 1 jam |
| **Total** | **~19.5 jam (~2.5 hari sprint)** |

---

*PRD ini adalah living document — update sesuai kebutuhan selama development.*
