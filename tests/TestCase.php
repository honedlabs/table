<?php

declare(strict_types=1);

namespace Honed\Table\Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

use function Orchestra\Testbench\workbench_path;

class TestCase extends Orchestra
{
    use RefreshDatabase;
    use WithWorkbench;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        Carbon::setTestNow(Carbon::parse('2000-01-01 00:00:00'));
        
        parent::setUp();

    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom([
            workbench_path('database/migrations'),
        ]);

        $this->loadMigrationsFrom(
            __DIR__.'/../database/migrations',
        );
    }
}
