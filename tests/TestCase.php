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
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2000-01-01 00:00:00'));
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'Honed\Table\TableServiceProvider',
            '--tag' => 'table-migrations',
            '--force' => true,
        ]);

        $this->loadMigrationsFrom([
            workbench_path('database/migrations'),
        ]);
    }
}
