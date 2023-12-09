## Techspace solutions

### Requirements:
- [Nginx HTTP Server `1.24.0`](https://httpd.apache.org/docs/2.4/install.html)
- [PHP `>=8.1`](https://www.php.net/releases/8.1/en.php)
- [PostgreSQL `15.5`](https://www.postgresql.org/)
- [Symfony `7.0.*`](https://symfony.com/releases/7.0)
- [Node.js `21.2.0` (includes npm 10.2.4)](https://nodejs.org/en/download) or higher

### Docker
- [Currently used] (https://github.com/alexfer/docker-symfony)

### 1. Clone repository
```bash
    git clone git@github.com:alexfer/techspace.git
```
### 2. Prepare configuration
You should change database configuration
```bash
    cd rechspace/
    cp .env.original .env
```

### 3. Install dependencies use Composer
Use [Composer](https://getcomposer.org/) install to download and install the package.
```bash
    composer install
```

### 4. Creating a database and fill it with data
```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load
```
### 4. Install JavaScript dependencies & Compile scripts
```bash
    npm install
    npm run dev --watch
```
