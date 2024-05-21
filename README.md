RgFly ecommerce solution.
============

- (Tailwind theme (dev))
- (Bootstrap theme (not full compatibility yet))

### Requirements:
- [Nginx HTTP Server `1.24.0`](http://nginx.org/en/CHANGES-1.24)
- [PHP `>=8.2`](https://www.php.net/releases/8.3/en.php)
- [PostgreSQL `15.5`](https://www.postgresql.org/)
- [Symfony `7.0.*`](https://symfony.com/releases/7.0)
- [Node.js `21.6.2` (includes npm 10.5.1)](https://nodejs.org/en/download) or higher
- [Yarn `1.22.22`](https://classic.yarnpkg.com/en/docs/install)

### Docker
- [Currently used](https://github.com/alexfer/rgfly/tree/main/docker)

### 1. Clone repository
```shell
    $ git clone git@github.com:alexfer/rgfly.git
```
### 2. Prepare configuration
You should change database configuration
```shell
    $ cd projectdir/
    $ cp .env.dist .env
```
### 3. Install dependencies use Composer
Use [Composer](https://getcomposer.org/) install to download and install the package.
```shell
    $ composer install
```
### 4. Creating a database and fill it with data
```shell
    $ php bin/console doctrine:database:drop --force
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:migrations:migrate
    $ php bin/console doctrine:fixtures:load
    $ php bin/console app:functions:import
```
### 4. Install JavaScript dependencies & Compile scripts
```shell
    $ npm install
    $ npm run (watch|dev|buld)
```
##### or
```shell
    $ yarn install
    $ yarn (watch|dev|buld)
```
Build production:
```shell
    $ npm run build
```
Code of Conduct
============
I as member, contributor, and leader pledge to make participation in  community a harassment-free experience for everyone, regardless of age, body size, visible or invisible disability, ethnicity, sex characteristics, gender identity and expression, level of experience, education, socio-economic status, nationality, personal appearance, race, caste, color, religion, or sexual identity and orientation.

Contributing
============
We welcome contributions to this project, including pull requests and issues (and discussions on existing issues).

If you'd like to contribute code but aren't sure what, the [issues list](https://github.com/alexfer/rgbfly/issues) is a good place to start.
If you're a first-time code contributor, you may find Github's guide to [forking projects](https://guides.github.com/activities/forking/) helpful.

All contributors (whether contributing code, involved in issue discussions, or involved in any other way) must abide by our [code of conduct](https://github.com/whiteoctober/open-source-code-of-conduct/blob/master/code_of_conduct.md).
