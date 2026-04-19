<?php

namespace Source\Domain\Account\Presentation\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_user');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('manage_user');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_user');
    }

    public function update(User $user, User $model): bool
    {
        if (!$user->hasPermissionTo('manage_user')) {
            return false;
        }

        if ($model->hasRole('SUPER_ADMIN') && !$user->hasRole('SUPER_ADMIN')) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        if (!$user->hasPermissionTo('manage_user')) return false;
        if ($model->hasRole('SUPER_ADMIN')) return false;
        return $user->id !== $model->id;
    }
}
