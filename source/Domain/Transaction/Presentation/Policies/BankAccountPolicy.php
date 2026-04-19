<?php

namespace Source\Domain\Transaction\Presentation\Policies;

use App\Models\User;
use Source\Domain\Transaction\Infrastructure\Models\BankAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_bank_account') || $user->hasRole('SUPER_ADMIN');
    }

    public function view(User $user, BankAccount $model): bool
    {
        return $user->hasPermissionTo('manage_bank_account') || $user->hasRole('SUPER_ADMIN');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_bank_account') || $user->hasRole('SUPER_ADMIN');
    }

    public function update(User $user, BankAccount $model): bool
    {
        return $user->hasPermissionTo('manage_bank_account') || $user->hasRole('SUPER_ADMIN');
    }

    public function delete(User $user, BankAccount $model): bool
    {
        return $user->hasPermissionTo('manage_bank_account') || $user->hasRole('SUPER_ADMIN');
    }
}
