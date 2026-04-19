<?php

namespace App\Filament\Exporters;

use Spatie\Activitylog\Models\Activity;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AuditLogExporter extends Exporter
{
    protected static ?string $model = Activity::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')
                ->label('Thời gian')
                ->formatStateUsing(fn ($state) => $state?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')),
            ExportColumn::make('causer.name')
                ->label('Người thực hiện'),
            ExportColumn::make('subject_type')
                ->label('Loại đối tượng')
                ->formatStateUsing(fn ($state) => class_basename($state)),
            ExportColumn::make('subject_id')
                ->label('ID Đối tượng'),
            ExportColumn::make('description')
                ->label('Hành động'),
            ExportColumn::make('properties')
                ->label('Chi tiết thay đổi')
                ->formatStateUsing(fn ($state) => json_encode($state, JSON_UNESCAPED_UNICODE)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Lịch sử Audit Log đã được xuất xong.';
    }

    public function getFileName(Export $export): string
    {
        return "Audit_log_" . now()->format('Ymd_His');
    }

    public static function getMaxRows(): int
    {
        return 50000;
    }
}
