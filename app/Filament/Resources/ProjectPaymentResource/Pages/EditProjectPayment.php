<?php

namespace App\Filament\Resources\ProjectPaymentResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\ProjectPaymentResource;
use App\Models\CoaThird;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EditProjectPayment extends EditRecord
{
    protected static string $resource = ProjectPaymentResource::class;

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
            Actions\DeleteAction::make()
                ->visible(function ($record) {
                    return $record->is_jurnal == 0;
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
        $record = $this->record;

        if ((float) $record->projectPaymentDetails->count() == 0) {
            Notification::make()
                ->title('Input detail terlebih dahulu.')
                ->danger()
                ->send();
            $this->halt();
        }

        $this->save();

        JournalRepository::ProjectPaymentPostJournal($this->record);
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
