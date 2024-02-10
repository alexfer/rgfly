Techspace solutions
============

### Requirements:
- [Nginx HTTP Server `1.24.0`](http://nginx.org/en/CHANGES-1.24)
- [PHP `>=8.3`](https://www.php.net/releases/8.3/en.php)
- [PostgreSQL `15.5`](https://www.postgresql.org/)
- [Symfony `7.0.*`](https://symfony.com/releases/7.0)
- [Node.js `21.2.0` (includes npm 10.2.4)](https://nodejs.org/en/download) or higher

### Docker
- [Currently used](https://github.com/alexfer/techspace/tree/v.0.1-dev/docker)

### 1. Clone repository
```bash
    $ git clone git@github.com:alexfer/techspace.git
```
### 2. Prepare configuration
You should change database configuration
```bash
    $ cd projectdir/
    $ cp .env.original .env
```

### 3. Install dependencies use Composer
Use [Composer](https://getcomposer.org/) install to download and install the package.
```bash
    $ composer install
```

### 4. Creating a database and fill it with data
```bash
    $ php bin/console doctrine:database:drop --force
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:migrations:migrate
    $ php bin/console doctrine:fixtures:load
```
### 4. Install JavaScript dependencies & Compile scripts
============
##### First deploy
```bash
    $ npm install
    $ npm run dev --watch
```
##### Interactive development
```bash    
    $ npm run watch
```
##### Production build
```bash    
    $ npm run build
```
Code of Conduct
============

I as member, contributor, and leader pledge to make participation in  community a harassment-free experience for everyone, regardless of age, body size, visible or invisible disability, ethnicity, sex characteristics, gender identity and expression, level of experience, education, socio-economic status, nationality, personal appearance, race, caste, color, religion, or sexual identity and orientation.

Contributing
============

We welcome contributions to this project, including pull requests and issues (and discussions on existing issues).

If you'd like to contribute code but aren't sure what, the [issues list](https://github.com/alexfer/techspace/issues) is a good place to start.
If you're a first-time code contributor, you may find Github's guide to [forking projects](https://guides.github.com/activities/forking/) helpful.

All contributors (whether contributing code, involved in issue discussions, or involved in any other way) must abide by our [code of conduct](https://github.com/whiteoctober/open-source-code-of-conduct/blob/master/code_of_conduct.md).
