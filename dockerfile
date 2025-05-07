# 🧱 Imagen base optimizada ya construida con grpc, imagick, supervisord, etc.
FROM europe-west1-docker.pkg.dev/maxmenu-447510/maxmenu-php-a/php82-grpc-imagick:latest

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY get/ /var/www/html/get/
WORKDIR /var/www/html

# 🔁 Copiar solo los archivos necesarios para Composer
COPY composer.json composer.lock ./

# 🔧 Instalar dependencias PHP
RUN echo "📦 Instalando dependencias con Composer..." && \
    composer install --no-dev --optimize-autoloader --no-interaction --no-progress || \
    (echo "❌ Composer falló" && exit 1)

# 📂 Copiar el resto del proyecto
COPY . /var/www/html

# 🔧 Configuraciones personalizadas
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# 🛠️ Apache rewrite + public como raíz
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf && \
    a2enmod rewrite && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# 📂 Crear estructura de logs
RUN mkdir -p /var/www/html/logs/apache2 /var/log/supervisord && \
    touch /var/www/html/logs/apache2/access.log /var/www/html/logs/apache2/error.log && \
    chmod -R 777 /var/www/html/logs /var/log/supervisord

# 🌐 Exponer puerto para Cloud Run
EXPOSE 8080

# 🏁 Iniciar supervisor que lanza Apache y gestiona logs
CMD ["/entrypoint.sh"]