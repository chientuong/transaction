<?php

namespace App\Domain\Transaction\Presentation\Filament\Exporters;

use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PaymentPrefixExporter extends Exporter
{
    protected static ?string $model = PaymentPrefix::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code')
                ->label('Mã Prefix'),
            ExportColumn::make('name')
                ->label('Tên hiển thị'),
            ExportColumn::make('description')
                ->label('Mô tả'),
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
        return 'Danh sách Payment Prefix đã được xuất xong.';
    }

    public function getFileName(Export $export): string
    {
        return "Danh_sach_payment_prefix_" . now()->format('Ymd_His');
    }
}
