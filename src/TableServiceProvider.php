<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Table\Commands\ColumnMakeCommand;
use Honed\Table\Commands\PurgeCommand;
use Honed\Table\Commands\TableMakeCommand;
use Honed\Table\Http\Controllers\TableViewController;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/table.php', 'table');

        $this->registerViewsMacro();
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'table');

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
     */
    protected function offerPublishing(): void
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

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/table'),
        ], 'table-lang');
    }

    /**
     * Register the route macro for the action handler.
     */
    protected function registerViewsMacro(): void
    {
        Router::macro('tableViews', function () {
            /** @var Router $this */

            /** @var string $endpoint */
            $endpoint = config('table.views.uri', '_views/{table}');

            $this->post($endpoint, [TableViewController::class, 'store'])
                ->name('table.views.store');

            $this->patch($endpoint, [TableViewController::class, 'update'])
                ->name('table.views.update');

            $this->put($endpoint, [TableViewController::class, 'destroy'])
                ->name('table.views.destroy');
        });
    }
}
