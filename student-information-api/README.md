<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

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

## Student Information Management API

This backend-only Laravel 12 REST API uses SQLite, Eloquent, Sanctum, PHPUnit, factories, and seeders.

### Setup

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File -Force database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

The API base URL is `http://localhost:8000/api/v1`. OpenAPI documentation is available at `/api/docs` and in [docs/openapi.yaml](docs/openapi.yaml).

The seeder creates 104 fictional users, including 100 student users linked one-to-one with the 100 seeded student profiles. The password for demonstration accounts is `password`; login with `admin@example.test`, `staff@example.test`, or `student@example.test`. The remaining student and instructor addresses are generated and can be inspected in the database.

### Domain rules

- Roles: `ADMINISTRATOR`, `STAFF`, `INSTRUCTOR`, `STUDENT`.
- Student year levels: 1 through 4. Student statuses: `ACTIVE`, `INACTIVE`, `GRADUATED`, `SUSPENDED`.
- Grades are percentages from 0 to 100, with one grade record per enrollment.
- Student numbers, course codes, program codes, and student/offering enrollment pairs are database-unique.
- DELETE endpoints deactivate academic records where history should be retained.
- Students can only view their own student, enrollment, grade, and academic-record data.
- Instructors can modify grades only for assigned course offerings.

### API groups

Authentication is provided by `/auth/login`, `/auth/logout`, and `/auth/me`. Resource groups are `/students`, `/programs`, `/courses`, `/academic-terms`, `/course-offerings`, `/enrollments`, and `/grades`. Academic history is available at `/students/{id}/academic-record`.

Collection endpoints are paginated. Students support `search`, `program_id`, `year_level`, `status`, `sort`, `page`, and `per_page`.

### Testing

Tests use an in-memory SQLite database and do not depend on development seed data:

```powershell
php artisan test
```

Passwords are hashed by Laravel's `hashed` cast. Password hashes and bearer tokens are excluded from user resources. Never commit `.env`, real credentials, or production secrets.

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
