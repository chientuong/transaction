<?php

namespace Tests\Feature\Domain\Transaction\Presentation\Filament\Resources;

use Source\Domain\System\Infrastructure\Models\Setting;
use Source\Domain\Transaction\Infrastructure\Models\BankAccount;
use App\Filament\Resources\BankAccountResource\Pages\CreateBankAccount;
use App\Filament\Resources\BankAccountResource\Pages\EditBankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BankAccountResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('bank_list', [
            ['bank_code' => 'VCB', 'bank_name' => 'Vietcombank'],
        ]);

        Permission::create(['name' => 'manage_bank_account', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo('manage_bank_account');
        $this->actingAs($user);
    }

    public function test_it_validates_account_number_on_create(): void
    {
        Livewire::test(CreateBankAccount::class)
            ->fillForm([
                'bank_code' => 'VCB',
                'account_number' => 'ABC123',
                'account_holder' => 'TEST HOLDER',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['account_number' => 'regex']);
    }

    public function test_it_validates_account_number_on_edit(): void
    {
        $record = BankAccount::factory()->create([
            'account_number' => '1234567890',
        ]);

        Livewire::test(EditBankAccount::class, [
            'record' => $record->getRouteKey(),
        ])
            ->fillForm([
                'account_number' => 'INVALID',
            ])
            ->call('save')
            ->assertHasFormErrors(['account_number' => 'regex']);
    }

    public function test_it_passes_numeric_account_number_with_leading_zero(): void
    {
        Livewire::test(CreateBankAccount::class)
            ->fillForm([
                'bank_code' => 'VCB',
                'account_number' => '0123456789',
                'account_holder' => 'TEST HOLDER',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors(['account_number']);

        $this->assertDatabaseHas('bank_accounts', [
            'account_number' => '0123456789',
        ]);
    }
}
