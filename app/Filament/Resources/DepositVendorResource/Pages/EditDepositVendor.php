<?php

namespace App\Filament\Resources\DepositVendorResource\Pages;

use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\DepositVendorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDepositVendor extends EditRecord
{
    protected static string $resource = DepositVendorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('post_jurnal')
                ->label('Post Journal')
                ->icon('heroicon-s-cash')
                ->action('postJournal')
                ->visible(function () {
                    $record = $this->record;
                    if ($record->is_jurnal == 0) {
                        return true;
                    }

                    return false;
                })
                ->requiresConfirmation()
                ->successNotificationMessage("Succesfully post to journal"),
            Actions\DeleteAction::make()
                ->visible(function () {
                    $record = $this->record;
                    if ($record->is_jurnal == 0) {
                        return true;
                    }

                    return false;
                }),
        ];
    }

    protected function beforeFill(): void
    {
        if ($this->record->is_jurnal == 1) {
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }

    public function postJournal()
    {
        JournalRepository::DepositVendorJournal($this->record);
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
