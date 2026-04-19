<?php

$providers = [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
];

if (in_array(env('APP_SERVICE', 'all'), ['all', 'admin'])) {
    $providers[] = App\Providers\Filament\AdminPanelProvider::class;
}

return $providers;
