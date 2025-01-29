<?php

declare(strict_types=1);

use Honed\Table\Concerns\ActsBeforeRetrieval;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Builder;

class ActsBeforeRetrievalTest
{
    use ActsBeforeRetrieval;
}

class ActsBeforeRetrievalMethodTest extends ActsBeforeRetrievalTest
{
    public function before(Builder $builder): void
    {
        $builder->orderBy('updated_by', 'asc');
    }
}

beforeEach(function () {
    $this->test = new ActsBeforeRetrievalTest;
    $this->method = new ActsBeforeRetrievalMethodTest;
});

it('checks if it exists', function () {
    expect($this->test)
        ->actsBeforeRetrieval()->toBeFalse();

    expect($this->method)
        ->actsBeforeRetrieval()->toBeTrue();
});

it('acts before retrieval', function () {
    $builder = Product::query();

    $this->method->beforeRetrieval($builder);

    expect($builder->getQuery()->orders)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'column' => 'updated_by',
            'direction' => 'asc',
        ]);
});
