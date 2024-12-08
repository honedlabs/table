<?php

declare(strict_types=1);

namespace Honed\Table\Tests;

use Inertia\Inertia;
use Honed\Table\Tests\Stubs\Status;
use Illuminate\Support\Facades\View;
use Honed\Table\TableServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Inertia\ServiceProvider as InertiaServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        View::addLocation(__DIR__.'/Stubs');
        Inertia::setRootView('app');
        config()->set('inertia.testing.ensure_pages_exist', false);
        config()->set('inertia.testing.page_paths', [realpath(__DIR__)]);
    }


    protected function defineDatabaseMigrations()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            // $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->default(Status::AVAILABLE->value);
            $table->unsignedInteger('price')->default(0);
            $table->boolean('best_seller')->default(false);
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            InertiaServiceProvider::class,
            TableServiceProvider::class,
        ];
    }
}
