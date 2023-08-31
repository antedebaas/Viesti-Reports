#!/bin/bash
echo "Get latest version from git"
git pull

echo "Install composer dependencies"
composer install

echo "Install service"
bash installservice.sh

echo "clear cache"
php bin/console cache:clear

echo "run migrations"
php bin/console doctrine:migrations:migrate --no-interaction --query-time

echo "install assets"
php bin/console assets:install public

echo "warmup cache"
php bin/console cache:warmup