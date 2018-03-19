#!/bin/sh

set -e

# Install php libraries.
if [[ -z "${GITHUB_KEY}" ]]; then
  echo "GITHUB_KEY is not available"
else
  omposer config -g github-oauth.github.com $GITHUB_KEY
fi

composer install --no-interaction --optimize-autoloader --no-progress
