FROM php:8.2-apache

# Install ekstensi PHP yang dibutuhkan aplikasi
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Aktifkan mod_rewrite untuk dukungan .htaccess
RUN a2enmod rewrite

# Izinkan .htaccess di document root
RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy source code ke web root
COPY . /var/www/html/

# Startup script khusus Render (lebih stabil untuk PORT dinamis)
RUN printf '%s\n' \
'#!/usr/bin/env bash' \
'set -e' \
'' \
'PORT="${PORT:-10000}"' \
'DOCROOT="/var/www/html"' \
'' \
'# Jika source ada di subfolder arfxt, pindahkan ke web root agar require_once(__DIR__ . "/includes/...") tetap valid.' \
'if [ -d "/var/www/html/arfxt" ] && [ ! -d "/var/www/html/includes" ]; then' \
'  cp -R /var/www/html/arfxt/. /var/www/html/' \
'fi' \
'' \
'cat > /etc/apache2/ports.conf <<EOF' \
'Listen ${PORT}' \
'EOF' \
'' \
'cat > /etc/apache2/sites-available/000-default.conf <<EOF' \
'<VirtualHost *:${PORT}>' \
'    ServerAdmin webmaster@localhost' \
'    DocumentRoot ${DOCROOT}' \
'    ErrorLog ${APACHE_LOG_DIR}/error.log' \
'    CustomLog ${APACHE_LOG_DIR}/access.log combined' \
'    <Directory ${DOCROOT}>' \
'        AllowOverride All' \
'        Require all granted' \
'    </Directory>' \
'</VirtualHost>' \
'EOF' \
'' \
'exec apache2-foreground' \
> /usr/local/bin/render-start.sh \
&& chmod +x /usr/local/bin/render-start.sh

WORKDIR /var/www/html

EXPOSE 10000

CMD ["/usr/local/bin/render-start.sh"]

