# FROM php:8.2-fpm

# # Installez les dépendances
# RUN apt-get update && apt-get install -y \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     libzip-dev \
#     unzip \
#     git \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install gd zip \
#     libicu-dev \
#     && docker-php-ext-install intl

# # Installez Composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# # Ajoute cette ligne pour installer mysql-client
# RUN apt-get update && apt-get install -y default-mysql-client

# # Configurez le répertoire de travail
# WORKDIR /var/www/html

# # Installe Node.js et npm
# RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
#     && apt-get install -y nodejs


# # Copiez les fichiers du projet
# COPY . /var/www/html

# RUN docker-php-ext-install mysqli pdo pdo_mysql

# RUN docker-php-ext-install opcache
# ADD opcache.ini $PHP_INI_DIR/conf.d/

# # Créez les répertoires nécessaires
# RUN mkdir -p /var/www/html/var/cache \
#     && chown -R www-data:www-data /var/www/html/var

# # Définir l'utilisateur
# USER www-data


# Dockerfile
FROM php:8.3-fpm

# Switch to root user
USER root

# Pour ajouter Composer à un Dockerfile
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Install required dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip git libicu-dev \
    default-mysql-client curl nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mysqli pdo_mysql opcache \
    && rm -rf /var/lib/apt/lists/*  
    # Clean up

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy project files into the container
COPY . /var/www/html

# Add the OPCache configuration file
ADD opcache.ini $PHP_INI_DIR/conf.d/

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/var/cache \
    && chown -R www-data:www-data /var/www/html/var

# Set the default user
USER www-data

# Expose port 9000 for FPM
EXPOSE 9000

# Command to run when the container launches
CMD ["php-fpm"]