#!/usr/bin/env bash
# BookMyDoc: complete project (Checkpoints 2 + 3 + 4).
# Run from the folder where you want the project created.
# Needs: PHP 8.2+, Composer, Node 20+, Git, and MySQL (or XAMPP).
set -e
HERE="$(cd "$(dirname "$0")" && pwd)"

composer create-project laravel/laravel bookmydoc
cd bookmydoc

composer require laravel/breeze --dev
php artisan breeze:install blade --no-interaction
php artisan install:api --no-interaction
composer require barryvdh/laravel-dompdf

# Copy the project files on top (routes, models, controllers, views, migrations, tests, CI)
cp -R "$HERE/overlay/." .

echo "APP_TIMEZONE=Asia/Kolkata" >> .env

npm install
npm run build

echo ""
echo "Next:"
echo "  1. Create an empty MySQL database named bookmydoc"
echo "  2. In .env set DB_CONNECTION=mysql, DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE=bookmydoc, DB_USERNAME, DB_PASSWORD"
echo "  3. php artisan migrate:fresh --seed"
echo "  4. php artisan test"
echo "  5. php artisan serve   (open http://127.0.0.1:8000)"
