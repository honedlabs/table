<?php

namespace Honed\Table;

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
            ]);
        }

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'honed-stubs');

        $this->publishes([
            __DIR__.'/../config/table.php' => config_path('table.php'),
        ], 'table-config');

        $this->registerRoutesMacro();
    }

    /**
     * @return array<int,class-string>
     */
    public function provides(): array
    {
        return [
            TableMakeCommand::class,
        ];
    }

    /**
     * Register the route macro for the Table class.
     */
    private function registerRoutesMacro(): void
    {
        Router::macro('table', function () {
            $endpoint = type(config('table.endpoint', '/actions/{table}'))->asString();

            /** @var \Illuminate\Routing\Router $this */
            $this->match(['post', 'patch', 'put'], $endpoint, [TableController::class, 'handle'])
                ->name('table.actions');
        });
    }
}
