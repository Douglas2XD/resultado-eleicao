FROM php:8.2-fpm

# Instala extensões e dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho correto
WORKDIR /var/www/Eleicao

# Copia todos os arquivos do projeto
COPY . .

# Instala dependências do Laravel
RUN composer install

# Dá permissão para as pastas de cache
RUN chmod -R 775 storage bootstrap/cache

# Abre a porta
EXPOSE 9000

CMD ["php-fpm"]
