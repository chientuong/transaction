<?php

namespace App\Filament\Pages;

use Source\Domain\System\Infrastructure\Models\Setting;
use Source\Domain\Transaction\Domain\Enums\OpsStatusEnum;
use Source\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.manage-settings';

    protected static ?string $navigationLabel = 'Cài đặt hệ thống';

    protected static ?string $title = 'Cài đặt hệ thống';

    protected static string|\UnitEnum|null $navigationGroup = 'Cấu hình hệ thống';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'api_key_sepay' => Setting::get('api_key_sepay'),
            'api_system' => Setting::get('api_system'),
            'setting_ttl' => Setting::get('setting_ttl'),
            'webhook_configs' => Setting::get('webhook_configs', []),
            'bank_list' => Setting::get('bank_list', []),
            'trigger_sync_status' => Setting::get('trigger_sync_status'),
            'trigger_ops_status' => Setting::get('trigger_ops_status'),
            'trigger_mode' => Setting::get('trigger_mode', 'AND'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Kết nối & Bảo mật')
                    ->schema([
                        TextInput::make('api_key_sepay')
                            ->label('Sepay API Key')
                            ->password()
                            ->revealable(),
                        TextInput::make('api_system')
                            ->label('Hệ thống API Token (Authorization)')
                            ->helperText('Dùng để xác thực các API nội bộ. Client phải gửi header Authorization: [Token] hoặc Bearer [Token].')
                            ->password()
                            ->revealable()
                            ->required(),
                        TextInput::make('setting_ttl')
                            ->label('Thời gian giao dịch')
                            ->suffix('phút')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ]),

                Section::make('Danh sách Ngân hàng')
                    ->description('Cấu hình danh sách ngân hàng hiển thị trong quản lý tài khoản.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('bank_list')
                            ->label('Danh sách')
                            ->schema([
                                TextInput::make('bank_code')
                                    ->label('Mã Ngân hàng')
                                    ->placeholder('e.g. VCB')
                                    ->required(),
                                TextInput::make('bank_name')
                                    ->label('Tên Ngân hàng')
                                    ->placeholder('e.g. Vietcombank')
                                    ->required(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['bank_name'] ?? null)
                            ->columns(2),
                    ]),

                Section::make('Điều kiện kích hoạt Webhook')
                    ->description('Cấu hình điều kiện để tự động gửi thông báo Webhook sang bên thứ 3.')
                    ->schema([
                        Select::make('trigger_mode')
                            ->label('Chế độ kích hoạt')
                            ->options([
                                'AND' => 'Cả 2 (Sync Status VÀ Ops Status đều khớp)',
                                'OR' => '1 trong 2 (Sync Status HOẶC Ops Status khớp)',
                            ])
                            ->default('AND')
                            ->required()
                            ->native(false),

                        Select::make('trigger_sync_status')
                            ->label('Sync Status')
                            ->options(SyncStatusEnum::class)
                            ->searchable()
                            ->multiple()
                            ->placeholder('Chọn các trạng thái Sync')
                            ->native(false)
                            ->required(fn (Get $get) => $get('trigger_mode') === 'AND')
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($get('trigger_mode') === 'OR' && ! empty($value) && ! empty($get('trigger_ops_status'))) {
                                        $fail('Trong chế độ "1 trong 2", chỉ được phép chọn trạng thái từ Sync Status HOẶC Ops Status.');
                                    }
                                },
                            ]),

                        Select::make('trigger_ops_status')
                            ->label('Ops Status')
                            ->options(OpsStatusEnum::class)
                            ->searchable()
                            ->multiple()
                            ->placeholder('Chọn các trạng thái Ops')
                            ->native(false)
                            ->required(fn (Get $get) => $get('trigger_mode') === 'AND')
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($get('trigger_mode') === 'OR' && ! empty($value) && ! empty($get('trigger_sync_status'))) {
                                        $fail('Trong chế độ "1 trong 2", chỉ được phép chọn trạng thái từ Sync Status HOẶC Ops Status.');
                                    }
                                },
                            ]),
                    ])
                    ->columns(3),

                Section::make('Cấu hình Webhook')
                    ->description('Cấu hình các Webhook để hệ thống đẩy dữ liệu sang bên thứ 3.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('webhook_configs')
                            ->label('Danh sách Webhook')
                            ->schema([
                                TextInput::make('url')
                                    ->label('Webhook URL')
                                    ->url()
                                    ->required(),
                                Select::make('method')
                                    ->label('Phương thức')
                                    ->options([
                                        'GET' => 'GET',
                                        'POST' => 'POST',
                                        'PUT' => 'PUT',
                                    ])
                                    ->default('POST')
                                    ->required(),
                                TextInput::make('api_key')
                                    ->label('API Key / Secret')
                                    ->password()
                                    ->revealable(),
                                TextInput::make('payload_data_key')
                                    ->label('Key chứa dữ liệu')
                                    ->placeholder('Mặc định: data')
                                    ->default('data')
                                    ->required(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['url'] ?? null)
                            ->columns(3),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Lưu thay đổi')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Đã lưu cấu hình thành công')
            ->success()
            ->send();
    }
}
