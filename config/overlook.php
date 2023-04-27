<?php

return [
    'includes' => [
        App\Filament\Resources\CustomerResource::class,
        App\Filament\Resources\EmployeeResource::class,
        App\Filament\Resources\ProjectPlanResource::class,
        App\Filament\Resources\ProjectCostResource::class,
        App\Filament\Resources\UserResource::class,
        App\Filament\Resources\VendorResource::class,
        App\Filament\Resources\BankAccountResource::class,
    ],
    'excludes' => [
        // App\Filament\Resources\Blog\AuthorResource::class,
    ],
];
