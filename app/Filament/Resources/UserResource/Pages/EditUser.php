<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn ($record) => $record->email_verified_at !== null),

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['verified'] = $data['email_verified_at'] !== null ? '1' : '0';
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record['email_verified_at'] = $data['verified'] == '1' ? now() : null;
        $record->update($data);
        return $record;
    }
}
