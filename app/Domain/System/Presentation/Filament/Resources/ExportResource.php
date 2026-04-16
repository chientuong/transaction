<?php

namespace App\Domain\System\Presentation\Filament\Resources;

use App\Domain\System\Presentation\Filament\Resources\ExportResource\Pages;
use Filament\Actions\Exports\Models\Export;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;

class ExportResource extends Resource
{
    protected static ?string $model = Export::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cloud-arrow-down';

    protected static ?string $navigationLabel = 'Lịch sử xuất file';

    protected static ?string $modelLabel = 'File đã xuất';

    protected static ?string $pluralModelLabel = 'Lịch sử xuất file';

    public static function getNavigationGroup(): ?string
    {
        return 'Hệ thống';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Thời gian xuất')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('exporter')
                    ->label('Loại dữ liệu')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'App\Domain\Transaction\Presentation\Filament\Exporters\TransactionExporter' => 'Giao dịch',
                        'App\Domain\Transaction\Presentation\Filament\Exporters\BankAccountExporter' => 'Tài khoản ngân hàng',
                        'App\Domain\Transaction\Presentation\Filament\Exporters\PaymentPrefixExporter' => 'Nguồn thu',
                        'App\Domain\Transaction\Presentation\Filament\Exporters\AuditLogExporter' => 'Audit Log',
                        default => class_basename($state),
                    }),
                TextColumn::make('total_rows')
                    ->label('Số dòng')
                    ->numeric(),
                TextColumn::make('successful_rows')
                    ->label('Thành công')
                    ->numeric(),
                TextColumn::make('completed_at')
                    ->label('Hoàn thành')
                    ->dateTime('d/m/Y H:i:s')
                    ->placeholder('Đang xử lý...')
                    ->sortable(),
            ])
            ->actions([
                Action::make('download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Export $record) => route('filament.exports.download', ['export' => $record, 'format' => 'xlsx']))
                    ->openUrlInNewTab()
                    ->visible(fn (Export $record) => $record->completed_at !== null),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExports::route('/'),
        ];
    }
}
