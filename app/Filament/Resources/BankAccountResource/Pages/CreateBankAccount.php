<?php

namespace App\Filament\Resources\BankAccountResource\Pages;

use App\Filament\Resources\BankAccountResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasLocalizedFormActions;

class CreateBankAccount extends CreateRecord
{
    use HasLocalizedFormActions;
    protected static string $resource = BankAccountResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Thêm mới tài khoản ngân hàng';
    }
}
