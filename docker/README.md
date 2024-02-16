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
DROP ROLE IF EXISTS rgfly;
CREATE USER rgfly WITH password 'rgfly';
ALTER USER rgfly WITH SUPERUSER;
ALTER USER rgfly CREATEDB;
CREATE DATABASE rgfly OWNER rgfly;
GRANT ALL PRIVILEGES ON DATABASE rgfly TO rgfly;
````
Deploy database:
````shell
$ docker exec -it bash
$ cd rgfly
$ rm -rfv migrations/*
$ php bin/console doctrine:database:drop --if-exists --force
$ php bin/console doctrine:database:create
$ php bin/console make:migration --no-interaction
$ php bin/console doctrine:migrations:migrate --no-interaction
$ php bin/console doctrine:fixtures:load --no-interaction
````
