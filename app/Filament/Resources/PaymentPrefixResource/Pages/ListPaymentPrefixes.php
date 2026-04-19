<?php

namespace App\Filament\Resources\PaymentPrefixResource\Pages;

use App\Filament\Resources\PaymentPrefixResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use App\Filament\Exporters\PaymentPrefixExporter;
use Filament\Resources\Pages\ListRecords;

class ListPaymentPrefixes extends ListRecords
{
    protected static string $resource = PaymentPrefixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(PaymentPrefixExporter::class)
                ->label('Xuất Excel')
                ->modalHeading('Xuất danh sách Nguồn thu'),
            Actions\CreateAction::make()->label('Thêm mới'),
        ];
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Danh sách nguồn thu';
    }
}
