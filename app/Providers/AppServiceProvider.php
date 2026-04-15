<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Domain\Account\Presentation\Policies\UserPolicy;
use Spatie\Permission\Models\Role;
use App\Domain\Account\Presentation\Policies\RolePolicy;
use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use App\Domain\Transaction\Presentation\Policies\PaymentPrefixPolicy;
use App\Domain\Transaction\Infrastructure\Models\BankAccount;
use App\Domain\Transaction\Presentation\Policies\BankAccountPolicy;
use App\Domain\Transaction\Infrastructure\Models\Transaction;
use App\Domain\Transaction\Presentation\Policies\TransactionPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(PaymentPrefix::class, PaymentPrefixPolicy::class);
        Gate::policy(BankAccount::class, BankAccountPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
