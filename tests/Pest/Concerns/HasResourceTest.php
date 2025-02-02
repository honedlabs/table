<?php

declare(strict_types=1);

use Honed\Table\Concerns\HasResource;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Builder;

class ResourceTable
{
    use HasResource;
}

class ResourceMethodTest extends ResourceTable
{
    public function resource()
    {
        return Product::query()
            ->where('price', '>', 100);
    }
}

beforeEach(function () {
    $this->test = new ResourceTable;
    $this->method = new ResourceMethodTest;
});

it('guesses resource from name', function () {
    expect($this->test->guessResource())->toBe('\\App\\Models\\Resource');
});

it('sets resource', function () {
    $this->test->setResource(Product::query());
    expect($this->test->getResource())->toBeInstanceOf(Builder::class);
});

it('rejects null values', function () {
    $this->test->setResource(Product::query());
    $this->test->setResource(null);
    expect($this->test->getResource())->toBeInstanceOf(Builder::class);
});

it('gets model instance', function () {
    expect($this->method->getModel())
        ->toBeInstanceOf(Product::class);
});

it('gets model name', function () {
    expect($this->method->getModelName())
        ->toBe('Product');
});

it('gets model key', function () {
    expect($this->method->getModelKey())
        ->toBe('id');
});
