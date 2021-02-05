FROM gitlab.gbksoft.net:5005/int/images/php-apache-flex:7.3

ENV APACHE_ENV=production

COPY . /var/www/html/
RUN rm -rf /var/www/html/.git
RUN docker-php-ext-enable gd imagick mysqli pdo_mysql zip exif memcached 
RUN composer install --no-interaction --optimize-autoloader --no-progress --no-dev
RUN php init --env=Live --overwrite=All
RUN chown -R www-data:www-data /var/www/html
