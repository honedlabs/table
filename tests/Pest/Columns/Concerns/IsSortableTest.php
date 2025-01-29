<?php

use Honed\Core\Concerns\Evaluable;
use Honed\Core\Concerns\HasName;
use Honed\Table\Columns\Concerns\IsSortable;
use Honed\Table\Sorts\Sort;

class IsSortableTest
{
    use Evaluable;
    use HasName;
    use IsSortable;
}

beforeEach(function () {
    $this->test = new IsSortableTest;
    $this->test->setName('name');
});

it('is not `sortable` by default', function () {
    expect($this->test->isSortable())->toBeFalse();
});

it('sets sortable', function () {
    $this->test->setSortable(true);
    expect($this->test)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
        ->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('name')
        );
});

it('chains sortable', function () {
    expect($this->test->sortable(true))
        ->toBeInstanceOf(IsSortableTest::class)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
        ->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('name')
        );
});

it('can be set using setter', function () {
    $this->test->setSortable(true);
    expect($this->test->isSortable())->toBeTrue();
});

it('rejects null values', function () {
    $this->test->setSortable(true);
    $this->test->setSortable(null);
    expect($this->test)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
        ->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('name')
        );
});

it('can change column name', function () {
    expect($this->test->sortable('created_at'))
        ->toBeInstanceOf(IsSortableTest::class)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
        ->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('created_at')
        );
});
