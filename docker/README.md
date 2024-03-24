Creating and deploying postgres database
============
> [!IMPORTANT]
>  In the docker directory go over:
#### Make rhe followings command:
```shell
$ cd dev/projects/
$ git clone git@github.com:alexfer/rgfly.git
$ cd rgfly
$ composer install
$ npm install
$ npm run dev --watch
````
### Build docker
#### Run following commands and fill .env file:
````shell
$ cd docker
````
#### Fill out .env file:
````shell
$ cp .env.dist .env
$ docker-compose up -d --build
````
### Create database & grant privileges to database
Login to postgres container:
````shell
$ docker exec -it postgres sh
plsql -U postgres
````
Next step:
````sql
DROP ROLE IF EXISTS rgfly;
CREATE USER rgfly WITH password 'rgfly';
ALTER USER rgfly WITH SUPERUSER;
ALTER USER rgfly CREATEDB;
CREATE DATABASE rgfly OWNER rgfly;
GRANT ALL PRIVILEGES ON DATABASE rgfly TO rgfly;
````
Deploy database:
````
$ docker exec -it bash
$ cd rgfly
$ rm -rfv src/Migrations/migrations/*
$ touch src/Migrations/.gitkeep
$ php bin/console doctrine:database:drop --if-exists --force
$ php bin/console doctrine:database:create
$ php bin/console make:migration --no-interaction
$ php bin/console doctrine:migrations:migrate --no-interaction
$ php bin/console doctrine:fixtures:load --no-interaction
````
