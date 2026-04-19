<?php

namespace Source\Domain\Transaction\Presentation\Policies;

use App\Models\User;
use Source\Domain\Transaction\Infrastructure\Models\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_transaction') || $user->hasRole('SUPER_ADMIN');
    }

    public function view(User $user, Transaction $model): bool
    {
        return $user->hasPermissionTo('view_transaction_detail') || $user->hasRole('SUPER_ADMIN');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Transaction $model): bool
    {
        return false;
    }

    public function delete(User $user, Transaction $model): bool
    {
        return false;
    }

    public function confirm(User $user, Transaction $model): bool
    {
        return $user->hasPermissionTo('confirm_transaction') || $user->hasRole('SUPER_ADMIN');
    }

    public function reject(User $user, Transaction $model): bool
    {
        return $user->hasPermissionTo('reject_transaction') || $user->hasRole('SUPER_ADMIN');
    }
}
