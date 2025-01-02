<?php

namespace Honed\Table\Tests;

use Inertia\Inertia;
use Illuminate\Support\Facades\View;
use Honed\Table\TableServiceProvider;
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

    protected function getPackageProviders($app)
    {
        return [
            TableServiceProvider::class,
            InertiaServiceProvider::class,
        ];
    }

    protected function defineRoutes($router)
    {
        $router->middleware('web')->group(function ($router) {
            $router->get('/', fn () => Inertia::render('Home'))->name('home.index');
            $router->get('/products', fn () => Inertia::render('Products/Index'))->name('product.index');
            $router->get('/products/{product}', fn () => Inertia::render('Products/Show'))->name('product.show');
            $router->get('/products/create', fn () => Inertia::render('Products/Create'))->name('product.create');
        });
    }

    public function getEnvironmentSetUp($app) { }
}
