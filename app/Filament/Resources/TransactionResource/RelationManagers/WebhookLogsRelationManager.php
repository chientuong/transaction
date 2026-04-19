<?php

namespace App\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebhookLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'webhookLogs';

    protected static ?string $title = 'Lịch sử gọi Webhook';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Thời gian'),
                        Forms\Components\TextInput::make('method')
                            ->label('Phương thức'),
                        Forms\Components\TextInput::make('status_code')
                            ->label('Trạng thái'),
                    ]),
                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('payload')
                    ->label('Request Payload')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->rows(10)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('response_body')
                    ->label('Response Body')
                    ->rows(10)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('error_message')
                    ->label('Error Message')
                    ->columnSpanFull()
                    ->visible(fn ($record) => filled($record?->error_message)),
            ]);
    }

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
