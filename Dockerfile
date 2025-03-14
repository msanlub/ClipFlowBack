# Stage 1 - Build assets
FROM node:20 as node_builder

WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources/
RUN npm ci && npm run build

# Stage 2 - PHP y Composer
FROM php:8.3-fpm-alpine as php_base

RUN apk add --no-cache \
    postgresql-dev \
    nginx \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql opcache

# Configuración de PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/conf.d/opcache.ini $PHP_INI_DIR/conf.d/

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Stage 3 - Aplicación final
FROM php_base

# Configuración de Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/laravel.conf /etc/nginx/conf.d/default.conf

# Configuración de Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar aplicación
COPY . /var/www/html
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Permisos y optimización
WORKDIR /var/www/html
RUN chown -R www-data:www-data storage bootstrap/cache \
    && composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Variables de entorno
ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 80

COPY initialize-db.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/initialize-db.sh

CMD ["/usr/local/bin/initialize-db.sh", "/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
