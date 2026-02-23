FROM php:8.4-fpm

# Argumentos para UID/GID
ARG UID=1000
ARG GID=1000

# Instala dependências básicas
RUN apt-get update && apt-get install -y \
    git unzip curl sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Cria usuário com mesmo UID do host
RUN groupadd -g $GID laravel \
    && useradd -u $UID -g laravel -m laravel

# Define diretório
WORKDIR /var/www

# Copia composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Ajusta permissões
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R laravel:laravel /var/www

# Usa usuário não-root
USER laravel