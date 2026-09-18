#!/bin/bash
set -e

echo "=========================================="
echo "  Iniciando contenedor PHP-FPM"
echo "  Fecha: $(date)"
echo "  PHP: $(php -v | head -n1)"
echo "=========================================="

# Crear directorios necesarios si no existen
mkdir -p /var/www/storage/framework/views \
        /var/www/storage/logs \
        /var/www/bootstrap/cache \
        /var/www/public/vendor \
        /var/log

# Establecer permisos correctos
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public/vendor /var/log
chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/public/vendor

# Verificar permisos de archivos críticos
if [ -f "/var/www/.env" ]; then
    chmod 664 /var/www/.env
fi

# Ejecutar el comando principal (start.sh por defecto)
exec "$@"