<?php

namespace Honed\Table;

use Honed\Table\Console\Commands\TableMakeCommand;
use Illuminate\Support\Facades\Route;
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
            __DIR__.'/../config/table.php' => $this->app['path.config'].DIRECTORY_SEPARATOR.'table.php',
        ]);

        Route::macro('table', function () {
            Route::post(config('table.endpoint', '/actions'), [Table::class, 'handleAction']);
        });

        Route::bind('table', function (string $value) {
            try {
                $class = Table::decodeClass($value);

                if (! \class_exists($class)) {
                    abort(404);
                }

                if (! \is_subclass_of($class, Table::class)) {
                    abort(404);
                }

                return $class::make();
            } catch (\Throwable $th) {
                abort(404);
            }
        });
    }

    public function provides()
    {
        return [
            TableMakeCommand::class,
        ];
    }
}
