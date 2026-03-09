# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Aplikatif Base v1 — a reusable Laravel 12 admin template based on TailAdmin. Stack: Laravel 12, Tailwind CSS v4, Alpine.js, Vite, MySQL. UI text is in Indonesian (Bahasa).

## Common Commands

```bash
# Development (starts server, queue, logs, and Vite in parallel)
composer run dev

# Or manually:
php artisan serve       # Laravel dev server
npm run dev             # Vite with HMR

# Database
php artisan migrate --seed          # Run all migrations and seeders
php artisan optimize:clear          # Clear all caches

# Testing
composer run test                   # Pest (unit + feature), clears config first
npx playwright test                 # E2E tests (requires server running on :8000)
npx playwright test --debug         # E2E debug mode

# Build
npm run build                       # Production assets
```

## Architecture

### Auth
Custom auth controllers in `App\Http\Controllers\Auth\` (not Breeze/Fortify). Login has throttling (5 attempts/min), activity logging, and active-status checks.

### Authorization
Spatie/laravel-permission v7. Routes use `can:permission-name` middleware. Roles: superadmin, admin, staff. The sidebar menu in `app/Helpers/MenuHelper.php` filters items based on user permissions and roles.

### Routing
All routes in a single `routes/web.php`. Permission middleware applied per route group. Resource routes for standard CRUD.

### Settings System
Key-value store in `App\Models\Setting` with cache layer. Global `setting($key, $default)` helper in `app/Helpers/helpers.php` (autoloaded via composer.json).

### Activity Logging
Spatie/laravel-activitylog v4. User model has `LogsActivity` trait for automatic change tracking. Manual logging for auth events and settings changes via `activity()->causedBy($user)->log()`.

### Image Uploads
Uses `intervention/image` v3 with GD driver (no facade). Photos resized to 200x200, stored in `storage/app/public/photos/` with `uniqid()` naming.

### PWA
Service worker at `public/sw.js`, offline page at `public/offline.html`, dynamic manifest.json route. Controlled by settings: `pwa_enabled`, `pwa_theme_color`, `pwa_short_name`.

## Key Conventions

- **Validation**: Inline `$request->validate()` in controllers, not Form Request classes
- **Flash messages**: `redirect()->with('success', 'message')` pattern
- **Blade components**: Located in `resources/views/components/`, namespaced as `<x-common.card />`, `<x-form.input />`
- **Layouts**: `layouts/app.blade.php` (authenticated), `layouts/auth.blade.php` (guest)
- **Permissions per action**: `view users`, `create users`, `edit users`, `delete users` (space-separated verb + resource)

## Database

- MySQL with InnoDB engine (explicitly set in `config/database.php`)
- Host: 127.0.0.1, DB: aplikatif_base, user: root, no password
- Session/cache use file driver (not database)
- Tests use SQLite `:memory:`
- PHP 8.2 on this machine — use `--ignore-platform-req=php` for composer if needed

## Seeders (run order matters)

`DatabaseSeeder` runs: RolePermissionSeeder → AdminSeeder → SettingsSeeder. Default admin: admin@aplikatif.com / password (superadmin role).
