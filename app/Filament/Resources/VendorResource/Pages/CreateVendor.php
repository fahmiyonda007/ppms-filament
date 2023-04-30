<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\VendorResource;
use App\Models\CoaThird;
use App\Models\Vendor;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->user()->email;
        return static::getModel()::create($data);
    }
}
