#!/bin/bash
set -e

echo "=========================================="
echo "  Iniciando contenedor PHP-FPM + Assets"
echo "=========================================="

mkdir -p /var/www/storage/framework/views \
         /var/www/storage/framework/cache \
         /var/www/storage/framework/sessions \
         /var/www/storage/logs \
         /var/www/bootstrap/cache \
         /var/www/bootstrap/cache/views \
         /var/www/public/vendor

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public/vendor
chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/public/vendor
chmod -R 775 /var/www/storage/framework /var/www/storage/logs /var/www/bootstrap/cache/views

cd /var/www

if [ -f "composer.json" ] && [ ! -d "vendor" ]; then
  echo "Instalando dependencias PHP..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ -f "package.json" ] && [ ! -x "node_modules/.bin/vite" ]; then
  echo "Instalando dependencias NPM..."
  npm install --no-audit --no-fund --unsafe-perm=true
fi

if [ ! -f "public/build/manifest.json" ]; then
  echo "Compilando assets con Vite..."
  npm run build || {
    echo "Vite build falló. Continuando con PHP-FPM..."
  }
fi

php artisan view:clear >/dev/null 2>&1 || true
php artisan config:clear >/dev/null 2>&1 || true
php artisan route:clear >/dev/null 2>&1 || true
php artisan cache:clear >/dev/null 2>&1 || true

echo "Configuracion:"
echo "  - XDebug: $(php -r 'echo ini_get("xdebug.mode");')"
echo "  - OPCache: $(php -r 'echo ini_get("opcache.revalidate_freq");')s"
echo "  - PHP Memory: $(php -r 'echo ini_get("memory_limit");')"

echo "Iniciando PHP-FPM..."
exec php-fpm -F
