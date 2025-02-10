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
            __DIR__.'/../config/table.php' => config_path('table.php'),
        ], 'table-config');

        $this->configureEndpoint();
        $this->configureBindings();
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
     * Configure the route model binding for the Table class.
     */
    private function configureBindings(): void
    {
        Route::bind('table', function (string $value): Table {
            try {
                $class = Table::decode($value);

                if (! \class_exists($class) || ! \is_subclass_of($class, Table::class)) {
                    abort(404);
                }

                return $class::make();

            } catch (\Throwable $th) {
                abort(404);
            }
        });
    }

    /**
     * Configure the default endpoint for the Table class.
     */
    private function configureEndpoint(): void
    {
        Route::macro('table', function () {
            Route::post(Table::getDefaultEndpoint(), [Table::class, 'handleAction']);
        });
    }
}
