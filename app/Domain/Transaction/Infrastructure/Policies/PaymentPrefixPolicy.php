<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Infrastructure\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPrefixPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentPrefix');
    }

    public function view(AuthUser $authUser, PaymentPrefix $paymentPrefix): bool
    {
        return $authUser->can('View:PaymentPrefix');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentPrefix');
    }

    public function update(AuthUser $authUser, PaymentPrefix $paymentPrefix): bool
    {
        return $authUser->can('Update:PaymentPrefix');
    }

    public function delete(AuthUser $authUser, PaymentPrefix $paymentPrefix): bool
    {
        return $authUser->can('Delete:PaymentPrefix');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PaymentPrefix');
    }

    public function restore(AuthUser $authUser, PaymentPrefix $paymentPrefix): bool
    {
        return $authUser->can('Restore:PaymentPrefix');
    }

    public function forceDelete(AuthUser $authUser, PaymentPrefix $paymentPrefix): bool
    {
        return $authUser->can('ForceDelete:PaymentPrefix');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PaymentPrefix');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PaymentPrefix');
    }

    public function replicate(AuthUser $authUser, PaymentPrefix $paymentPrefix): bool
    {
        return $authUser->can('Replicate:PaymentPrefix');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PaymentPrefix');
    }

}