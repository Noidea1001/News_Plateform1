#!/bin/sh
set -e

# Render provides dynamic PORT environment variable (defaults to 80 if not set)
PORT="${PORT:-80}"

echo "Configuring Apache to listen on port ${PORT}..."

sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
