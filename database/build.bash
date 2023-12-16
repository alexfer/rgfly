#!/bin/bash

php bin/console doctrine:database:drop --if-exists --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction