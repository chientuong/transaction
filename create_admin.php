<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

echo "Running migrate...\n";
Artisan::call('migrate', ['--force' => true]);

echo "Generating shield permissions...\n";
Artisan::call('shield:generate', [
    '--all' => true,
    '--panel' => 'admin',
    '--no-interaction' => true
]);

echo "Creating or finding admin user...\n";
$user = User::firstOrCreate(
    ['email' => 'admin@example.com'],
    ['name' => 'Admin', 'password' => Hash::make('password')]
);

echo "Assigning super_admin role...\n";
$role = Role::firstOrCreate(['name' => 'SUPER_ADMIN', 'guard_name' => 'web']);
$user->assignRole($role);

echo "Account ready: admin@example.com / password\n";
