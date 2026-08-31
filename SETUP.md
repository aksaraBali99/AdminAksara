# Local Setup Guide

This is a Laravel 12 application. This guide covers getting it running on a local machine, including the Windows-specific quirks encountered when first setting this project up.

## Prerequisites

- **PHP 8.2+** (tested with PHP 8.4 via [Laravel Herd](https://herd.laravel.com/))
- **Composer 2.x**
- **Node.js 18+** and **npm**
- A database server: **MySQL/MariaDB** (e.g. via [Laragon](https://laragon.org/) or XAMPP) — or fall back to the bundled **SQLite** file for a zero-config start

Check what you have:

```bash
php -v
composer -V
node -v
npm -v
```

> On Windows, if `php`/`composer` aren't found in your regular terminal but you have Laravel Herd installed, they usually live at `C:\Users\<you>\.config\herd\bin\`. Use a PowerShell/terminal session where Herd's shims are on `PATH`.

## 1. Install PHP dependencies

```bash
composer install
```

**Known issue (Windows):** `composer install` can intermittently fail with:

```
The "https://codeload.github.com/.../legacy.zip/..." file could not be downloaded (HTTP/2 400)
```

This is a curl/HTTP2 flake talking to GitHub's codeload CDN, not a real problem with the package. Just retry:

```bash
composer install --prefer-dist
```

It typically succeeds within 1–3 attempts (already-downloaded packages are cached, so retries get faster). Avoid `--prefer-source` as a workaround — it clones full git history for every dependency and can take 20+ minutes.

## 2. Configure environment

```bash
cp .env.example .env
```

This project's `.env.example` ships with **blank values** (no defaults), so you must fill in `.env` yourself. At minimum set:

```env
APP_NAME="Aksara Virtual Admin"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

FILESYSTEM_DISK=local
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Then generate the app key:

```bash
php artisan key:generate
```

### Database: pick one

**Option A — SQLite (fastest, no server needed)**

A `database/database.sqlite` file already exists in the repo with schema/sample data. Set:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/project/database/database.sqlite
```

> `config/database.php` uses `env('DB_DATABASE', database_path('database.sqlite'))` — but because `.env.example` defines `DB_DATABASE=` (empty string, not unset), the default never kicks in. You must set the absolute path explicitly, or delete the empty `DB_DATABASE=` line from `.env` entirely so the default applies.

**Option B — MySQL/MariaDB (matches production)**

If you have a production SQL dump to restore from:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS your_db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p your_db_name < path/to/dump.sql
```

Then in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=root
DB_PASSWORD=your_local_mysql_password
```

If using **Laragon**, its MySQL root account may not be the default empty password — check with your team or Laragon's UI (tray icon → MySQL → reset root password) if login fails.

> ⚠️ Never commit a real production dump or `.env` with real credentials into the repo. Keep dump files outside the project directory (or add `*.sql` to `.gitignore` if you keep them nearby), and double-check `.env` stays untracked (it already is, via `.gitignore`).

Run migrations (safe to run even against a dump that already has some/all migrations applied — Laravel skips anything already recorded in the `migrations` table):

```bash
php artisan migrate
```

## 3. Fix Windows folder permissions (if needed)

If `composer install` or any `artisan` command fails with:

```
The .../bootstrap/cache directory must be present and writable.
```

...even though the folder clearly exists — this is a Windows quirk where Explorer sets the folder's **ReadOnly attribute**, which makes PHP's `is_writable()` report `false` even though writes actually succeed. Fix it with:

```powershell
attrib -R bootstrap\cache /S /D
attrib -R storage /S /D
```

## 4. Install JS dependencies and build assets

```bash
npm install
npm run build   # production build
# or
npm run dev     # dev server with hot reload
```

## 5. Run the app

The repo defines a convenience script:

```bash
composer run dev
```

This runs the PHP server, queue listener, log tailer (`laravel/pail`), and Vite together via `concurrently`.

**Known issue (Windows):** `laravel/pail` requires the `pcntl` PHP extension, which **does not exist on Windows**. It will crash, and because the script uses `--kill-others`, it takes down the PHP server and queue listener with it. On Windows, run things separately instead, skipping `pail`:

```bash
php artisan serve
```

in one terminal, and

```bash
npm run dev
```

in another.

**Known issue (Windows):** `php artisan serve` can fail to bind on every port it tries:

```
Failed to listen on 127.0.0.1:8000 (reason: ?)
...continues through 8001-8010...
```

...even when nothing else is using those ports. This appears to be a problem with `artisan serve`'s internal port-availability probe on some Windows/PHP setups (unaffected by whether MySQL/Laragon/etc. is running). The plain PHP built-in server works fine as a substitute:

```bash
php -S 127.0.0.1:8000 -t public
```

Then visit **http://127.0.0.1:8000**.

## Troubleshooting summary

| Symptom | Cause | Fix |
|---|---|---|
| `codeload.github.com ... HTTP/2 400` during `composer install` | Flaky GitHub CDN + curl/HTTP2 | Retry `composer install --prefer-dist` a couple of times |
| `bootstrap/cache directory must be present and writable` | Windows ReadOnly folder attribute confuses `is_writable()` | `attrib -R bootstrap\cache /S /D` and same for `storage` |
| `php artisan pail` throws `RuntimeException: [pcntl] extension is required` | `pcntl` isn't available on Windows PHP | Don't use `composer run dev`; run `php artisan serve` and `npm run dev` separately |
| `artisan serve` → `Failed to listen on 127.0.0.1:8000` (all ports) | Windows-specific port probe issue in `artisan serve` | Use `php -S 127.0.0.1:8000 -t public` instead |
| `DB_DATABASE=` empty in `.env` but app can't find `database.sqlite` | Empty string in `.env` overrides the `env()` default in `config/database.php` | Set the absolute path explicitly, or remove the line |
