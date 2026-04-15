<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebhookLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'webhookLogs';

    protected static ?string $title = 'Lịch sử gọi Webhook';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('method')
                    ->label('Phương thức')
                    ->badge(),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
                TextColumn::make('status_code')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn ($state) => $state >= 200 && $state < 300 ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('error_message')
                    ->label('Lỗi')
                    ->wrap()
                    ->color('danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->headerActions([])
            ->actions([
                ViewAction::make()->label('Chi tiết'),
            ])
            ->bulkActions([]);
    }
}
