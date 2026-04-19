<?php

namespace App\Filament\Resources;

use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\RoleResource\Pages\ListRoles;
use App\Filament\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Resources\RoleResource\Pages\EditRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    public static function getNavigationGroup(): ?string
    {
        return 'Quản trị hệ thống';
    }

    public static function getNavigationLabel(): string
    {
        return 'Quản lý vai trò';
    }

    public static function getModelLabel(): string
    {
        return 'Vai trò';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Danh sách vai trò';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tên vai trò (Mã hệ thống)')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->disabled(fn ($record) => $record && in_array($record->name, ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'])),
                
            CheckboxList::make('permissions')
                ->label('Quyền hạn')
                ->relationship('permissions', 'name')
                ->getOptionLabelFromRecordUsing(fn ($record) => match($record->name) {
                    'view_transaction' => 'Xem danh sách giao dịch',
                    'view_transaction_detail' => 'Xem chi tiết giao dịch',
                    'confirm_transaction' => 'Xác nhận giao dịch',
                    'reject_transaction' => 'Từ chối giao dịch',
                    'export_transaction' => 'Export giao dịch',
                    'manage_bank_account' => 'CRUD tài khoản ngân hàng',
                    'manage_payment_prefix' => 'Cấu hình Payment Prefix',
                    'manage_user' => 'Quản lý tài khoản nhân viên',
                    'assign_role' => 'Phân quyền user',
                    'view_summary_report' => 'Xem báo cáo tổng hợp',
                    'manage_system_config' => 'Cấu hình hệ thống (TTL, v.v.)',
                    default => $record->name,
                })
                ->columns(2)
                ->searchable()
                ->bulkToggleable()
                ->gridDirection('row'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên vai trò')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Số lượng quyền')
                    ->badge()
                    ->color('success'),
            ])
            ->actions([
                EditAction::make()->label('Chỉnh sửa'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
