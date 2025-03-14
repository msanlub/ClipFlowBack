# Usa una imagen base de PHP con Apache
FROM php:8.2-apache

# Instala dependencias del sistema necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    curl\
    libexif-dev \
    && docker-php-ext-install exif

# Instala extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql


# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos del proyecto Laravel al contenedor
COPY . .

# Instala las dependencias del proyecto Laravel
RUN composer install --no-interaction --optimize-autoloader

# Configura permisos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

RUN git config --global --add safe.directory /var/www/html

RUN chown -R www-data:www-data /var/www/html
USER www-data
USER root
    
# Habilita el módulo rewrite de Apache
RUN a2enmod rewrite

# Copia configuración personalizada de Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copia el script de entrada
COPY initialize-db.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/initialize-db.sh
# Expone el puerto 80
EXPOSE 80
# Establece el script de entrada
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Comando para iniciar Apache
CMD ["apache2-foreground"]
