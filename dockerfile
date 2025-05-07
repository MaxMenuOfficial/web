# Etapa 1: build con Composer
FROM php:8.2-apache as builder

# Instalar dependencias necesarias para extensiones
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install zip pdo

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código al contenedor
WORKDIR /var/www/html
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Copiar configuración de Apache si existe
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Etapa final: contenedor liviano
FROM php:8.2-apache

# Copiar todo del build anterior
COPY --from=builder /var/www/html /var/www/html
COPY --from=builder /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf

# Activar mod_rewrite
RUN a2enmod rewrite

# Configurar la raíz como public
WORKDIR /var/www/html/public

# Puerto por defecto de Cloud Run
EXPOSE 8080

CMD ["apache2-foreground"]