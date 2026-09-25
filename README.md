# LaundryKu

Aplikasi web laundry untuk katalog layanan, order pelanggan, verifikasi harga, tracking status, pembayaran, laporan, dan PWA.

## Stack produksi

- Laravel 13.33
- PHP 8.4+
- MySQL 8.4 LTS
- Blade + Alpine.js + Tailwind CSS 4 + Vite
- Laravel Fortify, session authentication, 2FA, dan passkeys
- Workbox melalui `vite-plugin-pwa`
- DomPDF dan Laravel Excel
- Pest/PHPUnit untuk test

## Project location

Source project berada di:

```text
C:\Projects\LaundryKu
```

Project sengaja tidak berada di OneDrive agar `vendor`, `node_modules`, dan file cache tidak ikut ter-sinkronisasi.

## Local development

PHP 8.4 dan MySQL 8.4 dapat dipasang terpisah dari XAMPP. Untuk pengembangan awal, `.env` lokal dapat memakai SQLite.

```powershell
.\tools\composer84.ps1 install
npm ci
Copy-Item .env.example .env
.\tools\php84.ps1 artisan key:generate
.\tools\php84.ps1 artisan migrate --seed
npm run build
.\tools\php84.ps1 artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk menjalankan server, queue, log, dan Vite secara bersamaan:

```powershell
.\tools\composer84.ps1 run dev
```

XAMPP memiliki PHP 8.2 dan mungkin tetap menjadi `php` default di PATH. Script `tools/php84.ps1` dan `tools/composer84.ps1` memastikan perintah project memakai PHP 8.4. Setelah terminal/VS Code dibuka ulang, Anda juga dapat mengganti PATH PHP secara permanen melalui pengaturan user.

Admin lokal dibuat oleh `AdminSeeder` hanya jika `ADMIN_EMAIL` dan `ADMIN_PASSWORD` diisi pada `.env`. Jangan pernah commit password tersebut.

## Database production

Salin `.env.example` menjadi konfigurasi staging/production dan isi:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://laundry.example.com
APP_TIMEZONE=Asia/Jakarta
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laundryku
DB_USERNAME=laundryku
DB_PASSWORD=secret-manager-value
SESSION_SECURE_COOKIE=true
```

Gunakan user database khusus dengan hak minimum. Jangan menggunakan `root` untuk aplikasi.

## Quality checks

```powershell
composer run lint
php artisan test
npm run build
composer audit
npm audit
```

## Current implemented scope

- Public landing page dan katalog.
- Registrasi pelanggan dengan normalisasi nomor telepon.
- Login, logout, email verification, password reset, 2FA, dan passkeys.
- Role admin, kasir, dan customer.
- Customer address dan online checkout.
- Server-side pricing untuk kilogram dan pcs.
- Idempotency key untuk mencegah duplicate checkout.
- Order state machine dan audit status.
- Approval harga bila perubahan melewati threshold.
- Payment recording dan verification.
- WhatsApp manual dengan log `prepared/opened/unknown`.
- Admin order dashboard, katalog management, laporan PDF, dan Excel.
- PWA dengan cache allowlist; data privat tidak dicache.

## Important production work still required

- Install MySQL 8.4 LTS dan buat database/user.
- Configure web server, HTTPS, queue worker, scheduler, dan backup.
- Set S3/private object storage for production proof files.
- Configure SMTP and Sentry/Nightwatch.
- Add end-to-end browser tests and UAT with the laundry owner.
- Perform restore test and release runbook before go-live.

Jangan mengubah `APP_DEBUG` menjadi `true` di production.
