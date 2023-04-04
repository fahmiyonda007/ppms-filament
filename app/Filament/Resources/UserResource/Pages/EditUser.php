<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn ($record) => auth()->user()->role !== 'sa' & $record->email_verified_at !== null),

            Action::make('Custom Button')
                ->action('Custom Button')
                ->requiresConfirmation()
                ->modalHeading('Delete posts')
                ->modalSubheading('Are you sure you\'d like to delete these posts? This cannot be undone.')
                ->modalButton('Yes, delete them')
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
