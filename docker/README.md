Creating and deploying postgres database
============
> [!IMPORTANT]
>  In the docker directory go over:
#### Make rhe followings command:
```shell
$ cd dev/projects/
$ git clone git@github.com:alexfer/rgbfly.git
$ cd rgbfly
$ composer install
$ npm install
$ npm run dev --watch
````
### Build docker
#### Run following commands:
````shell
$ cd docker
$ docker-compose up -d --buig
````
### Create database & grant privileges to database
Login to postgres container:
````shell
$ docker exec -it postgres sh
plsql -U postgres
````
Next step:
````sql
DROP ROLE IF EXISTS rgbfly;
CREATE USER rgbfly WITH password 'rgbfly';
ALTER USER rgbfly WITH SUPERUSER;
ALTER USER rgbfly CREATEDB;
CREATE DATABASE rgbfly OWNER rgbfly;
GRANT ALL PRIVILEGES ON DATABASE rgbfly TO rgbfly;
````
Deploy database:
````shell
$ docker exec -it bash
$ cd rgbbfly
$ rm -rfv migrations/*
$ php bin/console doctrine:database:drop --if-exists --force
$ php bin/console doctrine:database:create
$ php bin/console make:migration --no-interaction
$ php bin/console doctrine:migrations:migrate --no-interaction
$ php bin/console doctrine:fixtures:load --no-interaction
````
