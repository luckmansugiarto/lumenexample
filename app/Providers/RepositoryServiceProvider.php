<?php

namespace App\Providers;

use App\Http\Controllers\TeachingSessionController;
use App\Contracts\Repositories\TeachingSessionRepositoryInterface;
use App\Services\Repositories\TeachingSessionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private $contextualBindings = [
        TeachingSessionController::class => [
            TeachingSessionRepositoryInterface::class => TeachingSessionRepository::class,
        ]
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // foreach ($this->bindings as $interface => $concreteClass)
        // {
        //     $this->app->bind($interface, $concreteClass);
        // }

        foreach ($this->contextualBindings as $controller => $bindings)
        {
            foreach ($bindings as $interface => $concreteClasses)
            {
                $this->app->when($controller)
                    ->needs($interface)
                    ->give($concreteClasses);
            }
        }
    }
}
