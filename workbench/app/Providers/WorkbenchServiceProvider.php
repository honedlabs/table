<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Carbon::setTestNow(Carbon::parse('2000-01-01 00:00:00'));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
