<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Source\Domain\Account\Domain\Enums\RoleEnum;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_transaction',
            'view_transaction_detail',
            'confirm_transaction',
            'reject_transaction',
            'export_transaction',
            'manage_bank_account',
            'manage_payment_prefix',
            'manage_user',
            'assign_role',
            'view_summary_report',
            'manage_system_config',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => RoleEnum::SUPER_ADMIN->value]);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value]);
        $admin->syncPermissions([
            'view_transaction',
            'view_transaction_detail',
            'confirm_transaction',
            'reject_transaction',
            'export_transaction',
            'manage_bank_account',
            'manage_payment_prefix',
            'manage_user',
            'assign_role',
            'view_summary_report',
        ]);

        $operator = Role::firstOrCreate(['name' => RoleEnum::OPERATOR->value]);
        $operator->syncPermissions([
            'view_transaction',
            'view_transaction_detail',
            'confirm_transaction',
            'reject_transaction',
            'export_transaction',
        ]);
    }
}
