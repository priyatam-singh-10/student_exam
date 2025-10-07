<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## StudentExam Backend - Exam Form & Payment Portal

This Laravel backend provides APIs for exam forms, submissions, JWT authentication with role-based access, payments via Stripe or Razorpay (stubs plus webhooks), and PDF receipt generation using DomPDF.

### Requirements
- PHP 8.2+, Composer, MySQL/PostgreSQL

### Setup
1. Copy env and install
```bash
cp .env.example .env
composer install
php artisan key:generate
```
2. Configure DB in `.env`
3. JWT setup
```bash
php artisan vendor:publish --provider="PHPOpenSourceSaver\\JWTAuth\\Providers\\LaravelServiceProvider"
php artisan jwt:secret
```
4. (Optional) Publish DomPDF assets
```bash
php artisan vendor:publish --provider="Barryvdh\\DomPDF\\ServiceProvider"
```
5. Swagger docs
```bash
php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider"
php artisan l5-swagger:generate
```
6. Migrate
```bash
php artisan migrate
```
7. Serve
```bash
php artisan serve
```

### API Endpoints
- Auth: `/api/auth/register`, `/api/auth/login`, `/api/auth/me`, `/api/auth/logout`
- Forms: GET `/api/forms`, GET `/api/forms/{id}`, Admin: POST/PUT/DELETE `/api/forms{,/id}`
- Submissions: POST `/api/submissions`, GET `/api/submissions{,/id}`, PUT `/api/submissions/{id}`
- Payments: POST `/api/payments/initiate`, GET `/api/payments/{id}/receipt`, POST `/api/payments/sample-receipt`
- Webhooks: POST `/api/payments/webhook/stripe`, POST `/api/payments/webhook/razorpay`
- Swagger UI: `/api/documentation`

### Notes
- Amounts stored in smallest currency unit (paise/cents)
- Replace webhook handlers with real provider verification
- Receipts stored at `storage/app/receipts`

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
