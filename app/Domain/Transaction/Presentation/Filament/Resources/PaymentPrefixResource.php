<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources;

use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Actions\EditAction;
use App\Domain\Transaction\Presentation\Filament\Resources\PaymentPrefixResource\Pages\ListPaymentPrefixes;
use App\Domain\Transaction\Presentation\Filament\Resources\PaymentPrefixResource\Pages\CreatePaymentPrefix;
use App\Domain\Transaction\Presentation\Filament\Resources\PaymentPrefixResource\Pages\EditPaymentPrefix;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportBulkAction;
use App\Domain\Transaction\Presentation\Filament\Exporters\PaymentPrefixExporter;

class PaymentPrefixResource extends Resource
{
    protected static ?string $model = PaymentPrefix::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getNavigationGroup(): ?string
    {
        return 'Cấu hình chung';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tiền tố Nguồn thu';
    }

    public static function getModelLabel(): string
    {
        return 'Tiền tố Nguồn thu';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Danh sách nguồn thu';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tên Nguồn thu')
                ->required()
                ->maxLength(100),

            TextInput::make('prefix_code')
                ->label('Mã Tiền tố (Prefix)')
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true)
                ->regex('/^[a-zA-Z0-9_]+$/')
                ->disabled(fn ($record) => $record !== null)
                ->dehydrated()
                ->extraInputAttributes(['style' => 'text-transform: uppercase;'])
                ->helperText('Chỉ chứa ký tự chữ, số và gạch dưới. Tối đa 10 ký tự. Không thể sửa sau khi đã khởi tạo.'),

            Textarea::make('description')
                ->label('Mô tả chi tiết')
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Đang hoạt động')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên Nguồn thu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('prefix_code')
                    ->label('Mã Tiền tố')
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                IconColumn::make('is_active')
                    ->label('Hoạt động')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()->label('Chỉnh sửa'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(PaymentPrefixExporter::class)->label('Xuất các mục đã chọn'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentPrefixes::route('/'),
            'create' => CreatePaymentPrefix::route('/create'),
            'edit' => EditPaymentPrefix::route('/{record}/edit'),
        ];
    }
}
