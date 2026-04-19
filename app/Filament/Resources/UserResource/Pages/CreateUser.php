<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasLocalizedFormActions;

class CreateUser extends CreateRecord
{
    use HasLocalizedFormActions;
    protected static string $resource = UserResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Thêm mới tài khoản';
    }
}
