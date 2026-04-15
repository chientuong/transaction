<?php

namespace App\Domain\Account\Presentation\Filament\Resources\RoleResource\Pages;

use App\Domain\Account\Presentation\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Domain\System\Presentation\Filament\Traits\HasLocalizedFormActions;

class CreateRole extends CreateRecord
{
    use HasLocalizedFormActions;
    protected static string $resource = RoleResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Thêm mới vai trò';
    }
}
