# =========================================================
# Stage 1: Build frontend assets
# =========================================================
FROM node:22-alpine AS node-build

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm run build


# =========================================================
# Stage 2: Install PHP / Composer dependencies
# =========================================================
FROM composer:2 AS composer-build

WORKDIR /app

# Copy Composer files first for better Docker layer caching
COPY composer.json composer.lock ./

# Install production dependencies
# Do NOT use --ignore-platform-reqs
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts


# =========================================================
# Stage 3: Production Laravel application
# =========================================================
FROM php:8.4-apache

WORKDIR /var/www/html


# =========================================================
# Install system packages and PHP extensions
# =========================================================
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    gettext-base \
    unzip \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        mbstring \
        bcmath \
        intl \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# =========================================================
# Enable Apache modules
# =========================================================
RUN a2enmod rewrite


# =========================================================
# Copy Apache configuration template
# =========================================================
COPY docker/apache.conf.template \
    /etc/apache2/sites-available/000-default.conf.template


# =========================================================
# Copy Laravel application
# =========================================================
COPY . .


# =========================================================
# Copy Composer dependencies
# =========================================================
COPY --from=composer-build /app/vendor/ ./vendor/


# =========================================================
# Copy compiled Vite assets
# =========================================================
COPY --from=node-build /app/public/build/ ./public/build/


# =========================================================
# Prepare Laravel directories and permissions
# =========================================================
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
    && chmod -R 775 \
        storage \
        bootstrap/cache


# =========================================================
# Copy container entrypoint
# =========================================================
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh


# =========================================================
# Render's default port
# Render can override PORT at runtime.
# =========================================================
EXPOSE 10000


# =========================================================
# Start Laravel
# =========================================================
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]