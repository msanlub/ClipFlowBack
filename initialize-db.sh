#!/bin/sh
set -e

# Esperar a que la base de datos esté lista
until php artisan db:monitor --max-attempts=60 --sleep=1; do
  echo "Esperando a la base de datos..."
  sleep 1
done

# Comprobar si la base de datos ya está inicializada
if [ ! -f /var/www/html/.db_initialized ]; then
  echo "Inicializando la base de datos..."
  php artisan migrate --force
  php artisan db:seed --force
  php artisan storage:link
  touch /var/www/html/.db_initialized
else
  echo "La base de datos ya está inicializada."
  # Ejecutar solo migraciones pendientes
fi

# Iniciar la aplicación
exec "$@"
