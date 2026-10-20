
FROM php:8.4-fpm

# Dependency sistem untuk build ekstensi PHP + Node/NPM untuk Vite
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    ca-certificates \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*


RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    curl \
    fileinfo \
    xml \
    zip \
    intl \
    pcntl \
    opcache


RUN pecl install redis && docker-php-ext-enable redis


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN if [ -f composer.json ]; then composer install --no-interaction --prefer-dist --optimize-autoloader; fi

EXPOSE 9000

CMD ["php-fpm"]