<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources\TransactionResource\Pages;

use App\Domain\Transaction\Presentation\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Danh sách giao dịch';
    }

    protected function getHeaderActions(): array
    {
        return []; // No create button for transactions
    }
}
