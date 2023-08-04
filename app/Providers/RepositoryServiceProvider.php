<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Domain\Entities\Loan\ILoanRepository;
use App\Domain\Entities\Loan\IScheduledRepaymentRepository;
use App\Domain\Entities\User\IRoleRepository;
use App\Domain\Entities\User\IUserRepository;
use App\Infrastructure\Persistence\Database\Eloquent\Repositories\EloquentLoanRepository;
use App\Infrastructure\Persistence\Database\Eloquent\Repositories\EloquentRoleRepository;
use App\Infrastructure\Persistence\Database\Eloquent\Repositories\EloquentScheduledRepaymentRepository;
use App\Infrastructure\Persistence\Database\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            ILoanRepository::class,
            EloquentLoanRepository::class
        );

        $this->app->bind(
            IScheduledRepaymentRepository::class,
            EloquentScheduledRepaymentRepository::class
        );

        $this->app->bind(
            IUserRepository::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            IRoleRepository::class,
            EloquentRoleRepository::class
        );
    }
}
