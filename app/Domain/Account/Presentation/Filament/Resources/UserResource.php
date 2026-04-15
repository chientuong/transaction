<?php

namespace App\Domain\Account\Presentation\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Domain\Account\Domain\Enums\RoleEnum;
use App\Domain\Account\Domain\Enums\UserStatusEnum;
use App\Domain\Account\Presentation\Filament\Resources\UserResource\Pages;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Domain\Account\Application\Actions\ResetUserPasswordAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Quản lý tài khoản';
    }

    public static function getModelLabel(): string
    {
        return 'Tài khoản';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Danh sách tài khoản';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Họ tên')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                TextInput::make('password')
                    ->label('Mật khẩu tạm thời')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                
                Select::make('roles')
                    ->label('Vai trò')
                    ->relationship(
                        'roles', 
                        'name',
                        fn (Builder $query) => auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value)
                            ? $query
                            : $query->where('name', '!=', RoleEnum::SUPER_ADMIN->value)
                    )
                    ->preload()
                    ->disabled(fn (?User $record) => $record && $record->id === auth()->id())
                    ->required(),

                Select::make('status')
                    ->label('Trạng thái')
                    ->options(UserStatusEnum::class)
                    ->default(UserStatusEnum::ACTIVE->value)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Họ tên')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Vai trò')
                    ->formatStateUsing(fn ($state) => RoleEnum::tryFrom($state)?->getLabel() ?? $state)
                    ->badge()
                    ->color(fn ($state) => RoleEnum::tryFrom($state)?->getColor() ?? 'primary'),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge(),
                TextColumn::make('last_login_at')
                    ->label('Lần đăng nhập cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()->label('Chỉnh sửa'),
                Action::make('resetPassword')
                    ->label('Reset MK')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel('Xác nhận')
                    ->modalCancelActionLabel('Hủy')
                    ->action(function (User $record) {
                        $action = new ResetUserPasswordAction();
                        $newPass = $action->execute($record);
                        Notification::make()
                            ->title('Thành công')
                            ->body("Mật khẩu mới: {$newPass}")
                            ->success()
                            ->persistent()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
