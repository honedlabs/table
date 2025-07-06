<?php

declare(strict_types=1);

namespace Honed\Table\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

use function Orchestra\Testbench\workbench_path;

class TestCase extends Orchestra
{
    use RefreshDatabase;
    use WithWorkbench;

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom([
            workbench_path('database/migrations'),
        ]);

        $this->loadMigrationsFrom(
            __DIR__.'/../database/migrations',
        );
    }
}
