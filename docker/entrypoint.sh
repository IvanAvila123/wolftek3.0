#!/bin/sh
set -e

cd /var/www/html

[ -f .env ] || cp .env.example .env

echo "Esperando a la base de datos en ${DB_HOST}:${DB_PORT}..."
until mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl --silent 2>/dev/null; do
    sleep 2
done

grep -q '^APP_KEY=base64' .env || php artisan key:generate --force

php artisan package:discover --ansi
php artisan filament:upgrade --ansi || true
php artisan storage:link || true
php artisan migrate --force

# Primera vez: permisos de Shield + usuario administrador de demo
if [ "$(php artisan tinker --execute='echo \App\Models\User::count();' 2>/dev/null | tail -n1)" = "0" ]; then
    php artisan shield:generate --all --panel=admin --option=policies_and_permissions --no-interaction || true
    php artisan db:seed --force
    php artisan shield:super-admin --user=1 --panel=admin --no-interaction || true
fi

php artisan optimize:clear
chown -R www-data:www-data storage bootstrap/cache

exec "$@"
