#!/bin/sh

set -e

# Tune Apache user
test -f /var/www/html/docker-compose.yml && \
 {
  DCUID=`ls -lahn /var/www/html/docker-compose.yml | awk '{print $3;}'`
  DCGID=`ls -lahn /var/www/html/docker-compose.yml | awk '{print $4;}'`
  sed -ri "s/^www-data:x:[[:digit:]]+:[[:digit:]]+:www-data/www-data:x:${DCUID}:${DCGID}:www-data/" /etc/passwd
  sed -ri "s/^www-data:x:[[:digit:]]+:/www-data:x:${DCGID}:/" /etc/group
  echo "Run Apache with UID=${DCUID} GID=${DCGID}"
 }

# Enable SSL
test -f /var/www/html/.env && . /var/www/html/.env
test -z "${MAIN_DOMAIN}" || test -f /etc/apache2/ssl/ssl.key || \
    openssl req -newkey rsa:2048 -nodes -keyout /etc/apache2/ssl/ssl.key -x509 -days 3650 -out /etc/apache2/ssl/ssl.crt -batch \
    -subj "/C=UA/ST=UA/L=ZP/O=GBKSOFT/OU=IT/CN=${MAIN_DOMAIN}"
test -f /etc/apache2/ssl/ssl.key && a2ensite default-ssl

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

exec "$@"
