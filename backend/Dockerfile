FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite headers

RUN printf "<Directory /var/www/html>\nAllowOverride All\nRequire all granted\n</Directory>\n" \
    > /etc/apache2/conf-available/liftjourney.conf \
    && a2enconf liftjourney

WORKDIR /var/www/html

COPY backend/ /var/www/html/

EXPOSE 80
