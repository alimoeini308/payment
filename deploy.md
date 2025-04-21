# How to deploy

## 1. Manual

### Requirements

- Download & Install [PHP +8.2](https://www.php.net/downloads.php).
  <br> You can use [Xampp +8.2.12](https://www.apachefriends.org/download.html), or etc instead of manual download of PHP
- Download & Install [Composer 2.x](https://getcomposer.org/download/).

### Deployment Steps
- Composer Install
  <br> Run `composer install` command line into project directory.
- Run Migrations
  <br> Run `php artisan migrate` command line into project directory.
- Start the Server
  <br> Run `php artisan serve` command line into project directory.

### Run Project
Open [localhost:8000](https://localhost:8000) in your browser.

### Testing
  Run `php artisan test` command line into project directory.

<hr>

## 2. Dockerize

### Requirements
- Download & Install [Docker](https://www.docker.com/products/docker-desktop/).

### Deployment Steps
- Docker Run
  <br> Run `docker-compose up --build -d` command line into project directory.
- Run Migrations
  <br> Run `docker-compose exec app php artisan migrate` command line into project directory.

### Run Project
Open [localhost:8000](https://localhost:8000) in your browser.

### Testing
  Run `docker-compose exec app  php artisan test` command line into project directory.

