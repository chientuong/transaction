<?php

namespace App\Filament\Resources\PaymentPrefixResource\Pages;

use App\Filament\Resources\PaymentPrefixResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Traits\HasLocalizedFormActions;

class EditPaymentPrefix extends EditRecord
{
    use HasLocalizedFormActions;
    protected static string $resource = PaymentPrefixResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Chỉnh sửa tiền tố';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Xóa'),
        ];
    }
}
