#!/bin/sh

set -e

# Install php libraries.
echo "Start the update and the install"
composer config -g github-oauth.github.com $GITHUB_KEY
composer install --no-interaction --optimize-autoloader

echo "Run init"
php init

# Run database migrations.
echo "Run migration"
php ./yii migrate --interactive=0

# Run NPM.
echo "Run NPM"
cd design && npm i && npm run build
