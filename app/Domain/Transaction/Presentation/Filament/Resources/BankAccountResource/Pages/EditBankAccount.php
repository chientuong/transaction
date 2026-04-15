<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources\BankAccountResource\Pages;

use App\Domain\Transaction\Presentation\Filament\Resources\BankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Domain\System\Presentation\Filament\Traits\HasLocalizedFormActions;

class EditBankAccount extends EditRecord
{
    use HasLocalizedFormActions;
    protected static string $resource = BankAccountResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Chỉnh sửa tài khoản ngân hàng';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Tạm dừng')
                ->modalHeading('Tạm dừng Tài khoản')
                ->modalDescription('Bạn có chắc chắn muốn Tạm dừng hoạt động của tài khoản này?')
                ->modalSubmitActionLabel('Có, Tạm dừng')
                ->action(function ($record) {
                    $record->update(['is_active' => false]);
                    \Filament\Notifications\Notification::make()
                        ->title('Ghi nhận tạm dừng thành công')
                        ->success()
                        ->send();
                })
                ->visible(fn ($record) => $record->is_active),
        ];
    }
}
