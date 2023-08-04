<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Domain\Services\AuthService;
use App\Domain\Services\LoanService;
use Illuminate\Support\ServiceProvider;

class BindServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AuthService::class);
        $this->app->singleton(LoanService::class);
    }
}
