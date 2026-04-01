FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libsqlite3-dev

RUN docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# 🔥 CRIA .env a partir do exemplo
RUN cp .env.example .env

RUN composer install --no-dev--optimize-autoloader--no-interaction

# 🔑 agora funciona
RUN php artisan key:generate

# 🗄️ banco
RUN touch database/database.sqlite

# 🧱 migrations
RUN php artisan migrate --force

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000

ENV COMPOSER_MEMORY_LIMIT=-1