FROM php:8.2-apache

# Activar mod_rewrite
RUN a2enmod rewrite

# Copiar proyecto completo
COPY . /var/www/html

# Cambiar DocumentRoot a /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess y evitar 403
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' \
>> /etc/apache2/apache2.conf

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 8080
ENV APACHE_LISTEN_PORT=8080
RUN sed -i 's/80/${APACHE_LISTEN_PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${APACHE_LISTEN_PORT}/g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]