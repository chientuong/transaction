<?php

namespace App\Filament\Resources;

use Source\Domain\Transaction\Infrastructure\Models\Transaction;
use Source\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use Source\Domain\Transaction\Domain\Enums\OpsStatusEnum;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction as ActionsViewAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Source\Domain\System\Infrastructure\Models\Setting;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationGroup(): ?string
    {
        return 'Nghiệp vụ giao dịch';
    }

    public static function getNavigationLabel(): string
    {
        return 'Giao dịch thanh toán';
    }

    public static function getModelLabel(): string
    {
        return 'Giao dịch';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Danh sách Giao dịch';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema; // Transactions are not created/edited manually via CMS
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_code')
                    ->label('Mã Giao dịch')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('user_id')
                    ->label('User ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('prefix.name')
                    ->label('Nguồn thu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Số tiền')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('bankAccount.bank_code')
                    ->label('Tài khoản nhận')
                    ->formatStateUsing(function ($state) {
                        $bankList = Setting::get('bank_list', []);
                        $bank = collect($bankList)->firstWhere('bank_code', $state);
                        return $bank['bank_name'] ?? $state;
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sync_status')
                    ->label('Sync Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('ops_status')
                    ->label('Ops Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Thời gian tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Hết hạn lúc')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('confirmer.name')
                    ->label('NV Xác nhận')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('sync_status')
                    ->label('Sync Status')
                    ->options(SyncStatusEnum::class),

                SelectFilter::make('ops_status')
                    ->label('Ops Status')
                    ->options(OpsStatusEnum::class),

                SelectFilter::make('prefix_id')
                    ->label('Nguồn thu')
                    ->relationship('prefix', 'name'),

                SelectFilter::make('bank_account_id')
                    ->label('Tài khoản Ngân hàng')
                    ->relationship('bankAccount', 'bank_code'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Từ ngày'),
                        DatePicker::make('created_until')->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
               ActionsViewAction::make()->label('Chi tiết'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Thông tin Hệ thống')
                            ->schema([
                                TextEntry::make('transaction_code')->label('Mã Giao dịch')->copyable(),
                                TextEntry::make('user_id')->label('User ID'),
                                TextEntry::make('prefix.name')->label('Nguồn thu'),
                                TextEntry::make('amount')->label('Số tiền')->money('VND'),
                                TextEntry::make('bankAccount.bank_code')->label('Ngân hàng nhận'),
                                TextEntry::make('transfer_content')->label('Nội dung chuyển khoản')->copyable(),
                            ]),

                        Section::make('Trạng thái')
                            ->schema([
                                TextEntry::make('sync_status')->label('Kỹ thuật (Sync Status)')->badge(),
                                TextEntry::make('ops_status')->label('Vận hành (Ops Status)')->badge(),
                                TextEntry::make('confirmer.name')->label('NV Xác nhận'),
                                TextEntry::make('confirmed_at')->label('Thời điểm XN')->dateTime('d/m/Y H:i:s'),
                                TextEntry::make('ops_note')->label('Ghi chú vận hành'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivitiesRelationManager::class,
            RelationManagers\WebhookLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
