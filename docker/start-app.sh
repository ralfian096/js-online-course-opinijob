#!/usr/bin/env sh
set -eu

cd /var/www/API

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

# Ensure only mpm_prefork is enabled
a2dismod mpm_event || true
a2dismod mpm_worker || true
a2enmod mpm_prefork || true

exec apache2-foreground
