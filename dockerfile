FROM php:8.2-apache

# Activar mod_rewrite (muy común para cualquier web)
RUN a2enmod rewrite

# Copiar tu proyecto al public root
COPY . /var/www/html

# Establecer permisos básicos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto estándar de Apache en Cloud Run
EXPOSE 8080

# Cloud Run usa 8080, así que lo configuramos
ENV APACHE_LISTEN_PORT=8080
RUN sed -i 's/80/${APACHE_LISTEN_PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${APACHE_LISTEN_PORT}/g' /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]