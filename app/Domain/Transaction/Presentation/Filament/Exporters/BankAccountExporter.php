<?php

namespace App\Domain\Transaction\Presentation\Filament\Exporters;

use App\Domain\Transaction\Infrastructure\Models\BankAccount;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BankAccountExporter extends Exporter
{
    protected static ?string $model = BankAccount::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('bank_code')
                ->label('Mã Ngân hàng'),
            ExportColumn::make('bank_branch')
                ->label('Chi nhánh'),
            ExportColumn::make('account_number')
                ->label('Số tài khoản'),
            ExportColumn::make('account_holder')
                ->label('Chủ tài khoản'),
            ExportColumn::make('is_active')
                ->label('Đang hoạt động')
                ->formatStateUsing(fn ($state) => $state ? 'Có' : 'Không'),
            ExportColumn::make('created_at')
                ->label('Ngày tạo')
                ->formatStateUsing(fn ($state) => $state?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Danh sách tài khoản ngân hàng đã được xuất xong.';
    }

    public function getFileName(Export $export): string
    {
        return "Danh_sach_tai_khoan_ngan_hang_" . now()->format('Ymd_His');
    }
}
