#!/bin/bash

set -e

echo "========================================="
echo "Starting CafeFlow"
echo "========================================="

# Render provides PORT at runtime.
# Use 10000 if PORT is not provided.
PORT="${PORT:-10000}"

echo "Configuring Apache on port ${PORT}..."

# Generate the real Apache configuration
envsubst '${PORT}' \
    < /etc/apache2/sites-available/000-default.conf.template \
    > /etc/apache2/sites-available/000-default.conf

# Make sure the default site is enabled
a2ensite 000-default.conf > /dev/null


echo "Caching Laravel configuration..."

php artisan config:cache

echo "Caching Laravel routes..."

php artisan route:cache

echo "Caching Laravel views..."

php artisan view:cache


echo "========================================="
echo "CafeFlow is ready"
echo "Apache listening on port ${PORT}"
echo "========================================="

# Start Apache in foreground
exec apache2-foreground