#!/bin/sh

set -e

# Install php libraries.
echo "Start the update and the install"
composer config -g github-oauth.github.com $GITHUB_KEY
composer install --no-interaction --optimize-autoloader
