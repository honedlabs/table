<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Table\Commands\ColumnMakeCommand;
use Honed\Table\Commands\PurgeCommand;
use Honed\Table\Commands\TableMakeCommand;
use Honed\Table\Http\Controllers\TableController;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

use function trim;

class TableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/table.php', 'table');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMacros();

        if ($this->app->runningInConsole()) {
            $this->offerPublishing();

            $this->commands([
                ColumnMakeCommand::class,
                PurgeCommand::class,
                TableMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the publishing for the package.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'table-stubs');

        $this->publishes([
            __DIR__.'/../config/table.php' => config_path('table.php'),
        ], 'table-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'table-migrations');
    }

    /**
     * Register the router macros for the package.
     *
     * @return void
     */
    protected function registerMacros()
    {
        Router::macro('table', function () {
            /** @var Router $this */

            /** @var string $endpoint */
            $endpoint = config('table.endpoint', 'tables');

            $endpoint = trim($endpoint, '/');

            $methods = ['post', 'patch', 'put'];

            $this->match($methods, $endpoint, [TableController::class, 'dispatch'])
                ->name('tables');

            $this->match($methods, $endpoint.'/{table}', [TableController::class, 'invoke'])
                ->name('tables.invoke');
        });
    }
}
