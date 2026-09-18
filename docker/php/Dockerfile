FROM php:8.4-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

# Copiar scripts de configuración
COPY start.sh /usr/local/bin/start.sh
COPY www-fpm.conf /usr/local/etc/php-fpm.d/www.conf
RUN chmod +x /usr/local/bin/start.sh

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl unzip zip libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev \
    libicu-dev libonig-dev libxml2-dev libzip-dev libssl-dev \
    libpq-dev libldap2-dev default-mysql-client \
    ca-certificates gnupg pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Instalar Node.js 20 LTS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-webp --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli mbstring exif pcntl bcmath gd intl zip soap

# Instalar Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instalar XDebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Instalar Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["/bin/bash", "/usr/local/bin/start.sh"]