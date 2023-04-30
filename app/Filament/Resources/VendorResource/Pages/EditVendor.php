<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\VendorResource;
use App\Models\CoaThird;
use App\Models\Vendor;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $sumVendorDeposit = Vendor::where('id', '!=', $record->id)->sum('deposit') + $data['deposit'];
        CoaThird::where('name', Common::$depositToko)->update([
            'balance' => $sumVendorDeposit
        ]);
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }
}
