<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources;

use App\Domain\System\Infrastructure\Models\Setting;
use App\Domain\Transaction\Infrastructure\Models\BankAccount;
use App\Domain\Transaction\Presentation\Filament\Exporters\BankAccountExporter;
use App\Domain\Transaction\Presentation\Filament\Resources\BankAccountResource\Pages\CreateBankAccount;
use App\Domain\Transaction\Presentation\Filament\Resources\BankAccountResource\Pages\EditBankAccount;
use App\Domain\Transaction\Presentation\Filament\Resources\BankAccountResource\Pages\ListBankAccounts;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    public static function getNavigationGroup(): ?string
    {
        return 'Cấu hình chung';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tài khoản ngân hàng';
    }

    public static function getModelLabel(): string
    {
        return 'Tài khoản ngân hàng';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Danh sách Tài khoản NH';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('bank_code')
                ->label('Ngân hàng')
                ->options(function () {
                    return collect(Setting::get('bank_list', []))
                        ->pluck('bank_name', 'bank_code')
                        ->toArray();
                })
                ->searchable()
                ->required(),

            TextInput::make('bank_branch')
                ->label('Chi nhánh')
                ->maxLength(200),

            TextInput::make('account_number')
                ->label('Số tài khoản')
                ->required()
                ->unique(ignoreRecord: true)
                ->regex('/^\d+$/')
                ->validationMessages([
                    'regex' => 'Số tài khoản chỉ được chứa chữ số.',
                ])
                ->maxLength(50)
                ->dehydrated()
                ->helperText('Không thể chỉnh sửa sau khi tạo thành công.'),

            TextInput::make('account_holder')
                ->label('Tên chủ tài khoản')
                ->required()
                ->maxLength(200),

            Textarea::make('description')
                ->label('Mô tả / Ghi chú')
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Trạng thái Hoạt động')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_code')
                    ->label('Ngân hàng')
                    ->formatStateUsing(function ($state) {
                        $bankList = Setting::get('bank_list', []);
                        $bank = collect($bankList)->firstWhere('bank_code', $state);

                        return $bank['bank_name'] ?? $state;
                    })
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('account_number')
                    ->label('Số tài khoản')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('account_holder')
                    ->label('Tên chủ tài khoản')
                    ->searchable(),

                TextColumn::make('bank_branch')
                    ->label('Chi nhánh')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Hoạt động')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('bank_code')
                    ->label('Ngân hàng')
                    ->options(function () {
                        return collect(Setting::get('bank_list', []))
                            ->pluck('bank_name', 'bank_code')
                            ->toArray();
                    }),
                TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->placeholder('Tất cả')
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Tạm dừng'),
            ])
            ->actions([
                EditAction::make()->label('Chỉnh sửa'),
                DeleteAction::make()
                    ->label('Tạm dừng (Xóa)')
                    ->modalHeading('Tạm dừng Tài khoản')
                    ->modalDescription('Bạn có chắc chắn muốn Tạm dừng hoạt động của tài khoản này thay vì xóa? Tài khoản sẽ không hiện trên danh sách tùy chọn nhận tiền nữa.')
                    ->modalSubmitActionLabel('Xác nhận')
                    ->modalCancelActionLabel('Hủy')
                    ->action(function ($record) {
                        $record->update(['is_active' => false]);
                        Notification::make()
                            ->title('Ghi nhận tạm dừng thành công')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->is_active),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(BankAccountExporter::class)->label('Xuất các mục đã chọn'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankAccounts::route('/'),
            'create' => CreateBankAccount::route('/create'),
            'edit' => EditBankAccount::route('/{record}/edit'),
        ];
    }
}
