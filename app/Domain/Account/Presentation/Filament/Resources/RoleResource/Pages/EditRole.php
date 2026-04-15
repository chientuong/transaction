<?php

namespace App\Domain\Account\Presentation\Filament\Resources\RoleResource\Pages;

use App\Domain\Account\Presentation\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Domain\System\Presentation\Filament\Traits\HasLocalizedFormActions;

class EditRole extends EditRecord
{
    use HasLocalizedFormActions;
    protected static string $resource = RoleResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Chỉnh sửa vai trò';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Xóa'),
        ];
    }
}
