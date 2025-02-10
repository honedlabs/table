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
use Illuminate\Support\Str;
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

    protected function getPackageProviders($app)
    {
        return [
            InertiaServiceProvider::class,
            TableServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('seller_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->default(Status::Available->value);
            $table->unsignedInteger('price')->default(0);
            $table->boolean('best_seller')->default(false);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('category_product', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->primary(['category_id', 'product_id']);
            $table->timestamps();
        });
    }

    protected function defineRoutes($router)
    {
        $router->middleware(SubstituteBindings::class, EncryptCookies::class, AddQueuedCookiesToResponse::class)->group(function ($router) {
            $router->get('/', fn () => Inertia::render('Home'))->name('home.index');
            $router->get('/products', fn () => Inertia::render('Products/Index'))->name('products.index');
            $router->get('/products/{product}', fn () => Inertia::render('Products/Show'))->name('products.show');
            $router->get('/products/create', fn () => Inertia::render('Products/Create'))->name('products.create');
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:'.base64_encode(Str::random(32)));
    }
}
