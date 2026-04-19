<?php

namespace App\Filament\Exporters;

use Source\Domain\Transaction\Infrastructure\Models\Transaction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('transaction_code')
                ->label('Mã Giao dịch'),
            ExportColumn::make('user_id')
                ->label('User ID'),
            ExportColumn::make('prefix.name')
                ->label('Nguồn thu'),
            ExportColumn::make('amount')
                ->label('Số tiền'),
            ExportColumn::make('bankAccount.bank_code')
                ->label('Ngân hàng nhận'),
            ExportColumn::make('sync_status')
                ->label('Trạng thái Sync'),
            ExportColumn::make('ops_status')
                ->label('Trạng thái Ops'),
            ExportColumn::make('created_at')
                ->label('Thời gian tạo')
                ->formatStateUsing(fn ($state) => $state?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')),
            ExportColumn::make('confirmed_at')
                ->label('Thời gian xác nhận')
                ->formatStateUsing(fn ($state) => $state?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')),
            ExportColumn::make('confirmer.name')
                ->label('Người xác nhận'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Dữ liệu giao dịch của bạn đã được xuất xong và ' . number_format($export->successful_rows) . ' dòng đã được xử lý.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' dòng bị lỗi.';
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        // Format: {loại_dữ_liệu}_{prefix_nếu_có}_{ngày_từ}_{ngày_đến}
        // Since we don't easily have access to filters here without custom implementation,
        // we'll use a standard format for now and refine if possible.
        return "Danh_sach_giao_dich_" . now()->format('Ymd_His');
    }

    public static function getMaxRows(): int
    {
        return 50000;
    }
}
