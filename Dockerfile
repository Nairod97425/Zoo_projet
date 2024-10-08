FROM php:8.2-fpm

# Installez les dépendances
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip \
    && docker-php-ext-install pdo pdo_mysql\
    libicu-dev \
    && docker-php-ext-install intl

# Installez Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ajoute cette ligne pour installer mysql-client
RUN apt-get update && apt-get install -y default-mysql-client

# Configurez le répertoire de travail
WORKDIR /var/www/html

# Installe Node.js et npm
RUN apt-get update && apt-get install -y nodejs npm

# Copiez les fichiers du projet
COPY . /var/www/html

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN docker-php-ext-install opcache
ADD opcache.ini $PHP_INI_DIR/conf.d/

# Créez les répertoires nécessaires
RUN mkdir -p /var/www/html/var/cache \
    && chown -R www-data:www-data /var/www/html/var

# Définir l'utilisateur
USER www-data
