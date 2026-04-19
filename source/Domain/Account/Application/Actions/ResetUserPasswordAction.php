<?php

namespace Source\Domain\Account\Application\Actions;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswordAction
{
    /**
     * Set a new random temporary password for the user.
     * In a production environment, this would also fire an event or send an email.
     */
    public function execute(User $user, ?string $newPassword = null): string
    {
        $password = $newPassword ?? Str::random(10);
        
        $user->update([
            'password' => Hash::make($password)
        ]);
        
        return $password;
    }
}
