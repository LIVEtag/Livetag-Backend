#!/bin/bash

set -e

# Install php libraries.
if [[ -z "${GITHUB_KEY}" ]]; then
  echo "GITHUB_KEY is not available"
else
  composer config -g github-oauth.github.com $GITHUB_KEY
fi

composer install --no-interaction --optimize-autoloader --no-progress
