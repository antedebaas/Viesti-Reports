#!/bin/bash
echo "Get latest version from git"
git pull
echo "Install composer dependencies"
composer install

echo "clear cache and run migrations"
php bin/console cache:clear
php bin/console doctrine:migrations:migrate --no-interaction --query-time

echo "warmup cache and install assets"
php bin/console assets:install public
php bin/console cache:warmup