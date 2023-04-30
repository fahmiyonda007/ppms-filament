<?php

namespace App\Filament\Resources\CashTransferResource\Pages;

use App\Filament\Resources\CashTransferResource;
use App\Models\CoaThird;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCashTransfer extends CreateRecord
{
    protected static string $resource = CashTransferResource::class;

    protected function afterValidate()
    {
        $data = $this->data;

        $coaSource = (float)CoaThird::find($data['coa_id_source'])->balance;
        $data['source_end_balance'] = $coaSource - (float)$data['amount'];

        if ((float)$data['source_end_balance'] < 0) {
            Notification::make()
                ->title('Source End Balance tidak boleh < 0.')
                ->danger()
                ->send();
            $this->halt();
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $coaSource = CoaThird::find($data['coa_id_source']);
        $coaDestination = CoaThird::find($data['coa_id_destination']);
        $data['source_start_balance'] = (float)$coaSource->balance;
        $data['source_end_balance'] = (float)$coaSource->balance - (float)$data['amount'];
        $data['destination_start_balance'] = (float)$coaDestination->balance;
        $data['destination_end_balance'] = (float)$coaDestination->balance + (float)$data['amount'];

        $coaSource->balance = (float)$coaSource->balance - (float)$data['amount'];
        $coaSource->save();
        $coaDestination->balance = (float)$coaDestination->balance + (float)$data['amount'];
        $coaDestination->save();

        $data['created_by'] = auth()->user()->email;
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
