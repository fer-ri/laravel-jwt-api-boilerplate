## Laravel JWT API Boilerplate

[![Build Status](https://travis-ci.org/ghprod/laravel-jwt-api-boilerplate.svg?branch=master)](https://travis-ci.org/ghprod/laravel-jwt-api-boilerplate)

Based on latest stable version of Laravel (5.7).

### Features

- [x] CORS [barryvdh/laravel-cors](https://github.com/barryvdh/laravel-cors)
- [x] JWT Auth [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)
- [x] Email verification
- [x] Login throttle
- [x] JSON Custom error handler
- [ ] Role based user
- [ ] Docs

### Installation

```
cp .env.example
composer install
php artisan migrate:fresh --seed
php artisan key:generate
php artisan jwt:secret
```