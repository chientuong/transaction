<?php

namespace Source\Domain\Transaction\Presentation\Policies;

use App\Models\User;
use Source\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPrefixPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_payment_prefix') || $user->hasRole('SUPER_ADMIN');
    }

    public function view(User $user, PaymentPrefix $model): bool
    {
        return $user->hasPermissionTo('manage_payment_prefix') || $user->hasRole('SUPER_ADMIN');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_payment_prefix') || $user->hasRole('SUPER_ADMIN');
    }

    public function update(User $user, PaymentPrefix $model): bool
    {
        return $user->hasPermissionTo('manage_payment_prefix') || $user->hasRole('SUPER_ADMIN');
    }

    public function delete(User $user, PaymentPrefix $model): bool
    {
        return $user->hasPermissionTo('manage_payment_prefix') || $user->hasRole('SUPER_ADMIN');
    }
}
