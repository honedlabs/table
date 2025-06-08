<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Table\Commands\ColumnMakeCommand;
use Honed\Table\Commands\TableMakeCommand;
use Honed\Table\Contracts\ExportsTable;
use Honed\Table\Http\Controllers\TableController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

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

        /** @var string */
        $exporter = Config::get('table.exporter');

        $this->app->bind(ExportsTable::class, $exporter);
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
                TableMakeCommand::class,
                ColumnMakeCommand::class,
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
    }

    /**
     * Register the router macros for the package.
     *
     * @return void
     */
    protected function registerMacros()
    {
        Router::macro('table', function () {
            /** @var \Illuminate\Routing\Router $this */

            /** @var string $endpoint */
            $endpoint = config('table.endpoint', 'table');

            $endpoint = \trim($endpoint, '/');

            $methods = ['post', 'patch', 'put'];

            $this->match($methods, $endpoint, [TableController::class, 'dispatch'])
                ->name('table');

            $this->match($methods, $endpoint.'/{table}', [TableController::class, 'invoke'])
                ->name('table.invoke');
        });
    }
}
