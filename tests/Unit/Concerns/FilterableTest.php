<?php

use Honed\Table\Filters\Filter;

beforeEach(function () {
    $this->table = exampleTable();
    $this->blank = blankTable();
});

it('can determine if the table has no filters', function () {
    expect($this->blank->missingFilters())->toBeTrue();
    expect($this->blank->hasFilters())->toBeFalse();

    expect($this->table->missingFilters())->toBeFalse();
    expect($this->table->hasFilters())->toBeTrue();
});

it('can set filters', function () {
    $this->blank->setFilters([
        Filter::make('test'),
    ]);

    expect($this->blank->getFilters())
        ->toBeCollection()
        ->toHaveCount(1);
});

it('rejects null filters', function () {
    $this->table->setFilters(null);

    expect($this->table->getFilters())->not->toBeEmpty();
});

it('can get filters', function () {
    expect($this->table->getFilters())
        ->toBeCollection()
        ->not->toBeEmpty();

    expect($this->blank->getFilters())
        ->toBeCollection()
        ->toBeEmpty();
});
