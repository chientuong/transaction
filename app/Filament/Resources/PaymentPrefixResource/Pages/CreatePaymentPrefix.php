<?php

namespace App\Filament\Resources\PaymentPrefixResource\Pages;

use App\Filament\Resources\PaymentPrefixResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasLocalizedFormActions;

class CreatePaymentPrefix extends CreateRecord
{
    use HasLocalizedFormActions;
    protected static string $resource = PaymentPrefixResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Thêm mới tiền tố';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['prefix_code'])) {
            $data['prefix_code'] = strtoupper($data['prefix_code']);
        }
        return $data;
    }
}
