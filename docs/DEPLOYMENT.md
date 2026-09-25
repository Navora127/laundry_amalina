# Deployment LaundryKu

Dokumen ini adalah runbook awal untuk deployment Laravel 13 di server production.

## 1. Runtime

- PHP 8.4 atau 8.5
- Composer 2
- Node.js 22/24 dan npm
- MySQL 8.4 LTS
- HTTPS melalui reverse proxy
- Supervisor/systemd untuk queue worker
- Cron untuk scheduler Laravel

Development lokal dapat memakai SQLite, tetapi staging dan production harus memakai MySQL 8.4.

## 2. Server preparation

1. Buat user OS khusus aplikasi.
2. Install PHP 8.4 dengan extensions `bcmath`, `curl`, `fileinfo`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `zip`, dan `gd`.
3. Arahkan document root ke folder `public/`.
4. Jangan mengekspos root project sebagai document root.
5. Pastikan `storage` dan `bootstrap/cache` writable oleh user aplikasi.
6. Jalankan `php artisan storage:link` atau konfigurasi private object storage.

## 3. Database

Buat database dan user terpisah:

```sql
CREATE DATABASE laundryku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laundryku'@'127.0.0.1' IDENTIFIED BY 'GANTI_DENGAN_SECRET';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP
    ON laundryku.* TO 'laundryku'@'127.0.0.1';
FLUSH PRIVILEGES;
```

Gunakan secret manager atau environment server untuk password. Jangan menaruh password database di Git.

## 4. Release

```bash
composer install --no-dev --prefer-dist --no-interaction
npm ci
npm run build
cp .env.example .env # hanya jika environment belum memiliki .env
php artisan key:generate # hanya sekali per environment
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Untuk release yang sudah memiliki database, backup database sebelum menjalankan migration.

## 5. Queue dan scheduler

Contoh Supervisor:

```ini
[program:laundryku-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laundryku/artisan queue:work --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/laundryku/storage/logs/worker.log
```

Cron server:

```cron
* * * * * cd /var/www/laundryku && php artisan schedule:run >> /dev/null 2>&1
```

## 6. Environment production

```dotenv
APP_NAME="LaundryKu"
APP_ENV=production
APP_KEY=<dari-secret-manager>
APP_DEBUG=false
APP_URL=https://laundry.example.com
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
SESSION_SECURE_COOKIE=true
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laundryku
DB_USERNAME=laundryku
DB_PASSWORD=<secret>
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=s3
```

Pastikan `APP_DEBUG=false`, HTTPS, secure cookie, dan private object storage aktif.

## 7. Backup

- Dump database harian.
- Simpan backup di luar server utama.
- Gunakan object storage versioning untuk bukti pembayaran dan foto timbangan.
- Contoh retensi: daily 14, weekly 8, monthly 6.
- Lakukan restore test setiap bulan.
- Target awal: RPO 24 jam, RTO 4 jam. Gunakan managed database/PITR bila RPO lebih kecil dibutuhkan.

## 8. Observability

- Health check: `/up`.
- Monitor 5xx, queue failure, latency, disk, memory, SSL expiry, dan backup status.
- Aktifkan Sentry atau Nightwatch untuk production.
- Telescope hanya untuk development atau harus dibatasi dengan autentikasi.
- Mask nomor telepon, alamat, token, dan password pada log.

## 9. Go-live checklist

- [ ] `APP_DEBUG=false`
- [ ] HTTPS dan secure cookie
- [ ] MySQL 8.4 dan user non-root
- [ ] Migration production berhasil
- [ ] Queue worker dan scheduler berjalan
- [ ] Backup dan restore test
- [ ] PWA manifest dan service worker build
- [ ] PDF/Excel export diuji
- [ ] WhatsApp manual diuji
- [ ] Role/policy/IDOR diuji
- [ ] UAT dengan owner laundry
- [ ] Rollback tag Git dan release note tersedia
