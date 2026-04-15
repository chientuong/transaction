<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources\PaymentPrefixResource\Pages;

use App\Domain\Transaction\Presentation\Filament\Resources\PaymentPrefixResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentPrefixes extends ListRecords
{
    protected static string $resource = PaymentPrefixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Thêm mới'),
        ];
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Danh sách nguồn thu';
    }
}
