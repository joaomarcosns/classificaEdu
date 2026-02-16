# ClassificaEdu - Setup & Troubleshooting

## Quick Start Commands

Run these commands to get the system working:

```bash
# 1. Clear all caches
php artisan optimize:clear

# 2. Run migrations and seed database
php artisan migrate:fresh --seed

# 3. Clear Filament cache specifically
php artisan filament:cache-components

# 4. Regenerate autoload files
composer dump-autoload
```

## Access the System

- **URL**: http://localhost:8000/admin
- **Email**: test@example.com
- **Password**: password

## If Menu is Still Empty

If the navigation menu doesn't show Students/Grades/Observations:

```bash
# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate
php artisan config:cache
php artisan route:cache
```

## Using Docker/Sail

If you're using Docker or Laravel Sail, prefix commands with:

```bash
./vendor/bin/sail artisan optimize:clear
./vendor/bin/sail artisan migrate:fresh --seed
```

Or with docker-compose:

```bash
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan migrate:fresh --seed
```

## Features Available

✅ **Student Management** - Create, edit, view students
✅ **Grade Entry** - Record grades per trimester (auto-classification)
✅ **Observations** - Daily behavioral log with categories
✅ **Reports** - Comprehensive HTML reports with impact analysis
✅ **pt-BR Interface** - All UI in Brazilian Portuguese

## System Architecture

```
Students
  ├─ Grades → Triggers automatic classification
  ├─ Observations → Behavioral tracking
  └─ Classification → Básico/Intermediário/Avançado
```

## Classification Thresholds

- **Básico**: < 6.0
- **Intermediário**: 6.0 - 7.9
- **Avançado**: ≥ 8.0

## Troubleshooting

### "Nothing in the menu"
Run: `php artisan optimize:clear`

### "Class not found" errors
Run: `composer dump-autoload`

### Database errors
Run: `php artisan migrate:fresh --seed`

### Filament errors
Run: `php artisan filament:cache-components`
