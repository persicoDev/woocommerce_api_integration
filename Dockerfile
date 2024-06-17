FROM php:8.1.20-fpm

# Install necessary system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath

# Install MongoDB extension manually
RUN curl -L -o mongodb-1.15.0.tgz https://pecl.php.net/get/mongodb-1.15.0.tgz \
    && pecl install mongodb-1.15.0.tgz \
    && docker-php-ext-enable mongodb \
    && rm mongodb-1.15.0.tgz


# Install PHP Extension Installer and additional extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow Composer to run as superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY ./app /var/www/html

RUN if [ -f composer.json ]; then \
        composer install --no-scripts; \
        # composer dump-autoload; \
        # composer install; \
    fi

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]