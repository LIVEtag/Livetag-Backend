#!/bin/sh

set -e

# Tune Apache user
test -f /var/www/html/docker-compose.yml && \
 {
  test -z "${DCUID}" && DCUID=`ls -lahn /var/www/html/docker-compose.yml | awk '{print $3;}'`
  test -z "${DCGID}" && DCGID=`ls -lahn /var/www/html/docker-compose.yml | awk '{print $4;}'`
  sed -ri "s/^www-data:x:[[:digit:]]+:[[:digit:]]+:www-data/www-data:x:${DCUID}:${DCGID}:www-data/" /etc/passwd
  sed -ri "s/^www-data:x:[[:digit:]]+:/www-data:x:${DCGID}:/" /etc/group
  chown www-data:www-data /var/www
  echo "Run Apache with UID=${DCUID} GID=${DCGID}"
 }

# Export environments
test -f /var/www/html/.env && \
 {
  for envstr in `cat /var/www/html/.env | grep -vE "^;"`
   do
    export ${envstr}
   done
 }
# Enable SSL
test -z "${MAIN_DOMAIN}" || test -f /etc/apache2/ssl/ssl.key || \
 {
  echo "authorityKeyIdentifier=keyid,issuer\nbasicConstraints=CA:FALSE\nkeyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment\nsubjectAltName = @alt_names\n\n" > /tmp/v3.ext
  echo "[alt_names]\nDNS.1 = ${MAIN_DOMAIN}\nDNS.2 = *.${MAIN_DOMAIN}\n" >> /tmp/v3.ext
  openssl genrsa -out /etc/apache2/ssl/TRUST.key 2048
  openssl req -x509 -new -nodes -key /etc/apache2/ssl/TRUST.key -sha256 -days 3650 -subj "/C=UA/ST=ZP/L=ZP/O=GBKSOFT/CN=gbksoft.loc" -out /etc/apache2/ssl/TRUST.crt
  openssl genrsa -out /etc/apache2/ssl/ssl.key 2048
  openssl req -new -newkey rsa:2048 -sha256 -nodes -key /etc/apache2/ssl/ssl.key -subj "/C=UA/ST=ZP/L=ZP/O=GBKSOFT/CN=${MAIN_DOMAIN}" -out /etc/apache2/ssl/ssl.csr
  openssl x509 -req -in /etc/apache2/ssl/ssl.csr -CA /etc/apache2/ssl/TRUST.crt -CAkey /etc/apache2/ssl/TRUST.key -CAcreateserial -out /etc/apache2/ssl/ssl.crt -days 3650 -sha256 -extfile /tmp/v3.ext
  openssl verify -CAfile /etc/apache2/ssl/TRUST.crt /etc/apache2/ssl/ssl.crt || { echo "SSL certificate fail"; exit; }
 }
test -f /etc/apache2/ssl/TRUST.crt && sed -ri "s/#SSLCACertificateFile \/etc\/apache2\/ssl.crt\/ca-bundle.crt/SSLCACertificateFile \/etc\/apache2\/ssl\/TRUST.crt/" /etc/apache2/sites-available/default-ssl.conf
test -f /etc/apache2/ssl/ssl.key && a2ensite default-ssl

echo "ServerName ${MAIN_DOMAIN}" >> /etc/apache2/conf-enabled/options.conf

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- apache2-foreground "$@"
fi

exec "$@"
