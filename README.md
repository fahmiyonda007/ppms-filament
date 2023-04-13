<p align="center">
    <img src="https://user-images.githubusercontent.com/41773797/131910226-676cb28a-332d-4162-a6a8-136a93d5a70f.png" alt="Banner" style="width: 100%; max-width: 800px;" />
</p>

<p align="center">
    <a href="https://github.com/filamentphp/filament/actions"><img alt="Tests passing" src="https://img.shields.io/badge/Tests-passing-green?style=for-the-badge&logo=github"></a>
    <a href="https://laravel.com"><img alt="Laravel v8.x" src="https://img.shields.io/badge/Laravel-v8.x-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://laravel-livewire.com"><img alt="Livewire v2.x" src="https://img.shields.io/badge/Livewire-v2.x-FB70A9?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.0" src="https://img.shields.io/badge/PHP-8.0-777BB4?style=for-the-badge&logo=php"></a>
</p>

Admin panel, form builder and table builder for Laravel. Built with the TALL stack. Designed for humans.


## Intallation

```bash
git clone https://github.com/fahmiyonda007/ppms-filament.git
```

```bash
composer install
```

Copy file **.env.example**, rename to **.env**, setup your .env file and generate key with:

```bash
php artisan key:generate
```

```bash
php artisan migrate
```

```bash
php artisan ser
php artisan serve
```

optional for optimize:

```bash
php artisan optimize:clear 
```



## Laravel - Filament

Generate resource:
```bash
php artisan make:filament-resource Bank --generate
```

Create model and setup manually your field:
```bash
php artisan make:model Bank
```

create migration file OR Generate migration file from existing table:
```bash
php artisan migrate:generate --tables="banks"
```

## Permissions
After create migration files, add this code:

```php
\\...
DB::statement("CALL SetNewPermission('bank')");
```
