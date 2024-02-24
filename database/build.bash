#!/bin/bash

rm -rfv src/Migrations/migrations/*
touch src/Migrations/.gitkeep

php bin/console doctrine:database:drop --if-exists --force
php bin/console doctrine:database:create
php bin/console make:migration --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction