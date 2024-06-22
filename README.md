# GSMART API
[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://travis-ci.org/joemccann/dillinger)

#### Requirement
- [Composser](https://getcomposer.org/download/)
- Laravel >= 9.x
- PHP >= 8.0 - 8.1
#### Setup Installation
1. Clone Repository`git clone -b dev https://bitbucket.gmf-aeroasia.co.id/scm/gdo/gsmart-api.git`
2. go to project folder. `cd gsmart-api`
3. Save as the. `env.example` to `.env` and set your database.
4. `composer install`
5. Install [Laravel/Sanctum](https://laravel.com/docs/9.x/sanctum) dependencie
   ```sh
   composer require laravel/sanctum
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   php artisan key:generate
   ```
 6. Install [Laravel/Spatie](https://spatie.be/docs/laravel-permission/v5/installation-laravel) dependencie
       ```sh
       composer require spatie/laravel-permission
       php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
       php artisan migrate:refresh --seed
       ```
7. Run the web server
   `php artisan serve`
8. After running the web server, open this address in your browser:
   `http://127.0.0.1:8000`


