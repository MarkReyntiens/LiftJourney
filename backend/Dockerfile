FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN printf "<Directory /var/www/html>\nAllowOverride All\nRequire all granted\n</Directory>\n" \
    > /etc/apache2/conf-available/liftjourney.conf \
    && a2enconf liftjourney

WORKDIR /var/www/html

COPY backend/ /var/www/html/

EXPOSE 80
