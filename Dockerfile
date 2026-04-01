FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libsqlite3-dev

RUN docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# 🔥 cria .env
RUN cp .env.example .env

# 🔥 evita erro de memória
ENV COMPOSER_MEMORY_LIMIT=-1

# 📦 instala dependências
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 🔑 gera key
RUN php artisan key:generate

# 🗄️ cria banco corretamente
RUN mkdir -p database
RUN touch database/database.sqlite
RUN chmod -R 777 database

# 📁 permissões do laravel
RUN mkdir -p storage bootstrap/cache
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000