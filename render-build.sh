#!/usr/bin/env bash

# Instalar dependencias de Composer
./vendor/bin/sail composer install --no-dev --optimize-autoloader

# Cachear configuración y rutas
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache

# Ejecutar migraciones
./vendor/bin/sail artisan migrate --force

# Ejecutar seeders
./vendor/bin/sail artisan db:seed --force

# Construir assets con Vite 
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
