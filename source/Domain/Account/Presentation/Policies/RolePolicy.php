<?php

namespace Source\Domain\Account\Presentation\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('assign_role');
    }

    public function view(User $user, Role $model): bool
    {
        return $user->hasPermissionTo('assign_role');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('assign_role');
    }

    public function update(User $user, Role $model): bool
    {
        if (!$user->hasPermissionTo('assign_role')) return false;
        if ($model->name === 'SUPER_ADMIN' && !$user->hasRole('SUPER_ADMIN')) return false;
        return true;
    }

    public function delete(User $user, Role $model): bool
    {
        if (!$user->hasPermissionTo('assign_role')) return false;
        if (in_array($model->name, ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'])) return false; // Không xóa role lõi
        return true;
    }
}
