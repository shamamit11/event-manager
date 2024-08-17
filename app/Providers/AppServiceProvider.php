<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domains\Calendar\Repositories\EventRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\EloquentEventRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EventRepositoryInterface::class, EloquentEventRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom([
            database_path('migrations'),
            base_path('app/Infrastructure/Persistence/Migrations')
        ]);
    }
}
