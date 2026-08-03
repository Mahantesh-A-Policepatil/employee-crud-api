<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Employee CRUD API

Laravel API for the Employee CRUD React application. It uses Laravel Sanctum,
Spatie Laravel Permission, queued jobs, and database seeders.

## Initial setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan migrate:fresh --seed
php artisan serve
```

> `php artisan migrate:fresh --seed` drops every database table before recreating
> the schema and seed data. Use it only when resetting local development data.

Configure the database and queue connection in `.env` before running migrations.

## Queue worker

Start a queue worker whenever the application is running. Attendance imports and
other queued work will not be processed until this command is running:

```bash
php artisan queue:work
```

For local development, restart the worker after changing job code:

```bash
php artisan queue:restart
php artisan queue:work
```

## Artisan generation reference

### Models and migrations

```bash
php artisan make:model Employee -m
php artisan make:model Department -m
php artisan make:model Project -m
php artisan make:model Attendance -m
php artisan make:model NavigationItem -m
```

`User` is Laravel's default model, generated with the Laravel application.

### API controllers

```bash
php artisan make:controller API/AuthController
php artisan make:controller API/EmployeeController --api
php artisan make:controller API/DepartmentController --api
php artisan make:controller API/ProjectController --api
php artisan make:controller API/AttendanceController --api
php artisan make:controller API/PermissionController --api
php artisan make:controller API/RoleController --api
php artisan make:controller API/UserRoleController --api
php artisan make:controller API/EmployeeDashboardController --api
php artisan make:controller API/NavigationItemController --api
```

### Form requests

```bash
php artisan make:request LoginRequest
php artisan make:request RegisterRequest
php artisan make:request StoreEmployeeRequest
php artisan make:request UpdateEmployeeRequest
php artisan make:request StoreDepartmentRequest
php artisan make:request UpdateDepartmentRequest
php artisan make:request StoreProjectRequest
php artisan make:request UpdateProjectRequest
php artisan make:request StoreAttendanceRequest
php artisan make:request UpdateAttendanceRequest
php artisan make:request UploadAttendanceCsvRequest
php artisan make:request StorePermissionRequest
php artisan make:request UpdatePermissionRequest
php artisan make:request StoreRoleRequest
php artisan make:request UpdateRoleRequest
php artisan make:request StoreUserRoleRequest
php artisan make:request UpdateUserRoleRequest
php artisan make:request UpdateUserRequest
```

### Seeders and permissions

```bash
php artisan make:seeder RolePermissionSeeder
php artisan make:seeder NavigationPermissionSeeder
php artisan make:seeder EmployeeSeeder
php artisan make:seeder ProjectSeeder
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=NavigationPermissionSeeder
```

`NavigationPermissionSeeder` is the source of truth for left navigation items.
Add a module's `key`, `label`, `path`, `icon`, and `sort_order` to that seeder.
When it runs, it creates the navigation item and matching `create`, `read`,
`update`, and `delete` permissions. Admin receives every available permission.

### Useful checks

```bash
php artisan route:list
php artisan migrate:status
php artisan optimize:clear
```

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

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
