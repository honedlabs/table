<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Concerns\IsOptimizable;
use Honed\Table\Tests\Stubs\Product;

class IsOptimizableTest
{
    use IsOptimizable;
}

beforeEach(function () {
    IsOptimizableTest::shouldOptimize(false);
    $this->test = new IsOptimizableTest;
});

it('is not `optimizable` by default', function () {
    expect($this->test->isOptimized())->toBeFalse();
});

it('sets optimizable', function () {
    $this->test->setOptimize(true);
    expect($this->test->isOptimized())->toBeTrue();
});

it('configures globally', function () {
    IsOptimizableTest::shouldOptimize(true);
    expect($this->test->isOptimized())->toBeTrue();
});

it('optimizes a query', function () {
    $this->test->setOptimize(true);
    $builder = Product::query();
    $columns = collect([
        Column::make('name'),
        Column::make('price'),
    ]);

    $this->test->optimize($builder, $columns);

    expect($builder->getQuery()->columns)
        ->toEqual(['name', 'price']);
});

it('does not optimize a query if not optimizable', function () {
    $builder = Product::query();
    $columns = collect([
        Column::make('name'),
        Column::make('price'),
    ]);

    $this->test->optimize($builder, $columns);

    expect($builder->getQuery()->columns)
        ->toBeNull();
});
