# Utilise l'image PHP-FPM officielle
FROM php:8.2-fpm

# Installer les outils nécessaires pour la compilation
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zlib1g-dev \
    git \
    unzip \
    curl \
    libicu-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    gd \
    intl

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer \
    && composer --version

# Configurer le répertoire de travail
WORKDIR /var/www/html

# Copier le code source dans le conteneur
COPY backend /var/www/html

# Donner les permissions appropriées
RUN chown -R www-data:www-data /var/www/html

# Installer les dépendances de Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader || true

# Expose le port 9000 et démarrer le serveur php-fpm
EXPOSE 9000
CMD ["php-fpm"]
