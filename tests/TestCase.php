<?php

declare(strict_types=1);

namespace Honed\Table\Tests;

use Honed\Table\TableServiceProvider;
use Honed\Table\Tests\Stubs\Status;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

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

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Generate a random key for testing
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    protected function getPackageProviders($app)
    {
        return [
            InertiaServiceProvider::class,
            TableServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->default(Status::Available->value);
            $table->unsignedInteger('price')->default(0);
            $table->boolean('best_seller')->default(false);
            $table->timestamps();
        });
    }

    protected function defineRoutes($router)
    {
        $router->middleware(SubstituteBindings::class, EncryptCookies::class, AddQueuedCookiesToResponse::class)->group(function ($router) {
            $router->get('/', fn () => Inertia::render('Home'))->name('home.index');
            $router->get('/products', fn () => Inertia::render('Products/Index'))->name('product.index');
            $router->get('/products/{product}', fn () => Inertia::render('Products/Show'))->name('product.show');
            $router->get('/products/create', fn () => Inertia::render('Products/Create'))->name('product.create');
        });
    }
}
