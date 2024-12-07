<?php

use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\DateFilter;

beforeEach(function () {
    $this->filter = DateFilter::make('name');
});

it('can set a clause', function () {
    expect($this->filter->clause(Clause::Contains))->toBeInstanceOf(DateFilter::class)
        ->getClause()->toBe(Clause::Contains);
});

it('can be set using setter', function () {
    $this->filter->setClause(Clause::Contains);
    expect($this->filter->getClause())->toBe(Clause::Contains);
});

it('does not accept null values', function () {
    expect($this->filter->getClause())->toBe(Clause::Is);
    $this->filter->setClause(null);
    expect($this->filter->getClause())->toBe(Clause::Is);
});

it('checks if it has a clause', function () {
    expect($this->filter->hasClause())->toBeTrue();
    expect($this->filter->missingClause())->toBeFalse();
});

it('has shorthand for is', function () {
    expect($this->filter->is())->toBeInstanceOf(DateFilter::class)
        ->getClause()->toBe(Clause::Is);
});
