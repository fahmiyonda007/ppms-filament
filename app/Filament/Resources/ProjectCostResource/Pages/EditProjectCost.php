<?php

namespace App\Filament\Resources\ProjectCostResource\Pages;

use App\Filament\Resources\ProjectCostResource;
use App\Models\CoaThird;
use App\Models\ProjectCostDetail;
use App\Models\Vendor;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditProjectCost extends EditRecord
{
    protected static string $resource = ProjectCostResource::class;
    protected $listeners = ['refresh' => '$refresh'];

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function refreshForm()
    {
        $this->fillForm();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $totAmt = $record->projectCostDetails->sum('amount');
        $data['total_amount'] = $totAmt;
        $data['total_payment'] = $this->getSumPaymentSource($data);
        $data['updated_by'] = auth()->user()->email;

        if ($totAmt == 0) {
            $data['payment_status'] = 'NOT PAID';
        }

        $record->update($data);

        if ($data['payment_status'] == 'PAID') {
            $sources = [
                $this->getSource1($data),
                $this->getSource2($data),
                $this->getSource3($data),
            ];
            foreach ($sources as $key => $value) {
                if ($value['id'] !== 0) {
                    $qry = "update {$value['table']} set {$value['column']} = `{$value['column']}` - {$value['amount']} where id = {$value['id']}";
                    // DB::statement((string)$qry);
                }
            }
        }

        return $record;
    }

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    // }

    // protected function beforeSave(): void
    // {
    //     $dt = $this->record->projectCostDetails->where('project_cost_id', $this->record->id);
    //     $uniq = $dt->unique('coa_id');
    //     $not_unique = $dt->diff($uniq);

    //     if (count($not_unique) > 0) {
    //         Notification::make()
    //             ->title('COA is duplicate')
    //             ->danger()
    //             ->send();
    //         $this->halt();
    //     }
    // }

    protected function getSource1(array $data): array
    {
        $res = [
            "id" => 0,
            "table" => '',
            "column" => '',
            "amount" => 0,
            "method" => 'getSource1'
        ];
        $coaThird1 = 0;
        $coaThird = CoaThird::find($data['coa_id_source1']);
        if ($coaThird) {
            $cond = $coaThird->name == 'DEPOSIT TOKO' && $data['vendor_id'] != null;
            if ($cond) {
                $vendor = Vendor::find($data['vendor_id']);
                $coaThird1 = $vendor->deposit;
                $res['id'] = (int)$vendor->id;
                $res['table'] = 'vendors';
                $res['column'] = 'deposit';
            } else {
                $coaThird1 = $coaThird->balance;
                $res['id'] = (int)$coaThird->id;
                $res['table'] = 'coa_level_thirds';
                $res['column'] = 'balance';
            }
        }

        $res['amount'] = (float)$coaThird1;
        return $res;
    }

    protected function getSource2(array $data): array
    {
        $res = [
            "id" => 0,
            "table" => '',
            "amount" => 0,
            "method" => 'getSource2'
        ];

        $coaThird2 = CoaThird::find($data['coa_id_source2']);
        if ($coaThird2) {
            $res['id'] = (int)$coaThird2->id;
            $res['table'] = 'coa_level_thirds';
            $res['column'] = 'balance';
            $res['amount'] = (float)$coaThird2->balance ?? 0;
        }
        return $res;
    }

    protected function getSource3(array $data): array
    {
        $res = [
            "id" => 0,
            "table" => '',
            "amount" => 0,
            "method" => 'getSource3'
        ];

        $coaThird2 = CoaThird::find($data['coa_id_source3']);
        if ($coaThird2) {
            $res['id'] = (int)$coaThird2->id;
            $res['table'] = 'coa_level_thirds';
            $res['column'] = 'balance';
            $res['amount'] = (float)$coaThird2->balance ?? 0;
        }
        return $res;
    }

    protected function getSumPaymentSource(array $data): float
    {
        $coaThird1 = 0;
        $coaThird = CoaThird::find($data['coa_id_source1']);
        if ($coaThird) {
            $cond = $coaThird->name == 'DEPOSIT TOKO' && $data['vendor_id'] != null;
            if ($cond) {
                $vendor = Vendor::find($data['vendor_id']);
                $coaThird1 = $vendor->deposit;
            } else {
                $coaThird1 = $coaThird->balance;
            }
        }
        $coaThird2 = CoaThird::find($data['coa_id_source2'])->balance ?? 0;
        $coaThird3 = CoaThird::find($data['coa_id_source3'])->balance ?? 0;
        $sum = (float)$coaThird1 + (float)$coaThird2 + (float)$coaThird3;
        return $sum;
    }
}
