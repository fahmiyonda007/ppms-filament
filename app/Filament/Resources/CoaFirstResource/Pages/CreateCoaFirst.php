<?php

namespace App\Filament\Resources\CoaFirstResource\Pages;

use App\Filament\Resources\CoaFirstResource;
use App\Models\CoaFirst;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCoaFirst extends CreateRecord
{
    protected static string $resource = CoaFirstResource::class;

}
