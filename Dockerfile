FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    librabbitmq-dev \
    && docker-php-ext-install -j$(nproc) pdo_pgsql sockets \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install amqp \
    && docker-php-ext-enable amqp

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
