<?php

namespace App\Filament\Resources\ReceivableResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\ReceivableResource;
use App\Models\CoaThird;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditReceivable extends EditRecord
{
    protected static string $resource = ReceivableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('post_jurnal')
                ->label('Post Journal')
                ->icon('heroicon-s-cash')
                ->action('postJournal')
                ->visible(function () {
                    return $this->record->is_jurnal == 0;
                })
                ->requiresConfirmation(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
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
        JournalRepository::ReceivablePostJournal($this->record);
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
