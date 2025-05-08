FROM europe-west1-docker.pkg.dev/maxmenu-447510/maxmenu-php-a/php82-grpc-imagick:latest

ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress || exit 1

# SOLO copiar estructura inicial (se sobreescribirá con volume)
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN a2enmod rewrite && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && \
    echo '<Directory /var/www/html/public>\nOptions Indexes FollowSymLinks\nAllowOverride All\nRequire all granted\n</Directory>' >> /etc/apache2/apache2.conf

EXPOSE 8080
CMD ["/entrypoint.sh"]