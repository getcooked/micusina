#!/usr/bin/env bash

# Run this script from the Laravel project root on Hostinger after a Git deploy.
# It is intentionally safe to run more than once.
set -Eeuo pipefail

if command -v composer2 >/dev/null 2>&1; then
    COMPOSER_BIN="composer2"
elif command -v composer >/dev/null 2>&1; then
    COMPOSER_BIN="composer"
else
    echo "Composer 2 was not found. Install Composer 2 or use Hostinger's composer2 command." >&2
    exit 1
fi

if [[ ! -f .env ]]; then
    echo "Missing .env. Create it and set APP_KEY, APP_URL, database, mail, and payment settings first." >&2
    exit 1
fi

echo "Installing production dependencies with ${COMPOSER_BIN}..."
if ! "${COMPOSER_BIN}" install --prefer-dist --no-dev --no-interaction --no-progress --optimize-autoloader; then
    echo "The ZIP download failed; clearing Composer's cache and retrying from source..." >&2
    "${COMPOSER_BIN}" clear-cache || true
    "${COMPOSER_BIN}" install --prefer-source --no-dev --no-interaction --no-progress --optimize-autoloader
fi

php artisan migrate --force
php artisan storage:link --force
php artisan optimize:clear
php artisan optimize

echo "Hostinger deployment completed."
