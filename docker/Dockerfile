# Base image PHP + Apache
FROM php:8.2-apache

# Instala extensões do PHP necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    a2enmod rewrite

# Instala ferramentas úteis (opcional)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Copia o código-fonte para o container
COPY . /var/www/html/

# Configura permissões (ajuste conforme necessário)
RUN chown -R www-data:www-data /var/www/html

# Expõe a porta 80
EXPOSE 80