FROM php:8.2-cli

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git unzip curl libsqlite3-dev

# Instala extensões PHP
RUN docker-php-ext-install pdo pdo_sqlite

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório
WORKDIR /var/www

# Copia arquivos
COPY . .

# Instala dependências do Laravel
RUN composer install

# Cria banco SQLite
RUN touch database/database.sqlite

# Gera APP_KEY
RUN php artisan key:generate

# Roda migrations
RUN php artisan migrate --force

# Expõe porta
EXPOSE 10000

# Comando inicial
CMD php artisan serve --host=0.0.0.0 --port=10000