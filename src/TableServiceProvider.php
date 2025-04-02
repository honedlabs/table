<?php

namespace Honed\Table;

use Honed\Table\Console\Commands\ColumnMakeCommand;
use Honed\Table\Console\Commands\TableMakeCommand;
use Honed\Table\Http\Controllers\TableController;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/table.php', 'table');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TableMakeCommand::class,
                ColumnMakeCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs'),
            ], 'stubs');

            $this->publishes([
                __DIR__.'/../config/table.php' => config_path('table.php'),
            ], 'config');
        }

        $this->registerRoutesMacro();
    }

    /**
     * Register the route macro for the Table class.
     */
    private function registerRoutesMacro(): void
    {
        Router::macro('table', function () {
            $endpoint = type(config('table.endpoint', '/table'))->asString();

            /** @var \Illuminate\Routing\Router $this */
            $this->match(
                ['post', 'patch', 'put'],
                $endpoint,
                TableController::class
            )->name('table.actions');
        });
    }
}
