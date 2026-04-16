<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources\BankAccountResource\Pages;

use App\Domain\Transaction\Presentation\Filament\Resources\BankAccountResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use App\Domain\Transaction\Presentation\Filament\Exporters\BankAccountExporter;
use Filament\Resources\Pages\ListRecords;

class ListBankAccounts extends ListRecords
{
    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(BankAccountExporter::class)
                ->label('Xuất Excel')
                ->modalHeading('Xuất danh sách Tài khoản ngân hàng'),
            Actions\CreateAction::make()->label('Thêm mới'),
        ];
    }
}
