# Flowcurement ERP

Flowcurement is a Laravel + Livewire ERP system with modular features for:

- Business Partners (Clients/Suppliers)
- Items
- Quotations
- Sales Orders
- Delivery Receipts
- User Management (roles/permissions)

## Tech Stack

- PHP / Laravel
- Livewire
- Tailwind CSS
- Vite
- MySQL
- Spatie Permission

## Requirements

- PHP 8.1+ (recommended: PHP 8.2+)
- Composer 2+
- Node.js 18+ and npm 9+
- MySQL/MariaDB
- Git

## 1) Clone Repository

```bash
git clone <YOUR_REPO_URL> flowcurement
cd flowcurement
```

## 2) Install Dependencies

Install PHP dependencies (`vendor`):

```bash
composer install
```

Install JS dependencies (`node_modules`):

```bash
npm install
```

## 3) Environment Setup

Create `.env` from example:

```bash
cp .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

Update database settings in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flowcurement
DB_USERNAME=root
DB_PASSWORD=
```

Generate app key:

```bash
php artisan key:generate
```

## 4) Database Setup

Run migrations and seeders:

```bash
php artisan migrate --seed
```

## 5) Storage Link (Required for uploads)

```bash
php artisan storage:link
```

## 6) Build Frontend Assets

Development:

```bash
npm run dev
```

Production:

```bash
npm run build
```

## 7) Run the Project

Using Laravel built-in server:

```bash
php artisan serve
```

Or use Laragon/XAMPP virtual host (if configured).

## Recommended After Pulling New Changes

```bash
composer install
npm install
php artisan migrate
php artisan optimize:clear
npm run build
```

## Troubleshooting

### A) Livewire 404 on update/upload endpoints

If you see paths like `.../flowcurement/flowcurement/livewire...`:

1. Check `APP_URL` in `.env`
2. Run:

```bash
php artisan optimize:clear
```

### B) Upload preview or file access issues

Make sure storage link exists:

```bash
php artisan storage:link
```

And ensure `FILESYSTEM_DISK=public` in `.env` if needed by your setup.

### C) Permission/403 errors

Re-run seeders:

```bash
php artisan db:seed
```

Then log out/login again.

## Notes

- `vendor/` and `node_modules/` are intentionally not pushed to GitHub.
- Keep `.env` private and never commit real secrets.

## License

Private/internal project.
