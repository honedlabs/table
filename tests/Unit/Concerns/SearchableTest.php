<?php

use Honed\Table\Table;
use Honed\Table\Filters\Filter;

beforeEach(function () {
    Table::useSearchName('search');
    Table::useScout(false);

    $this->table = exampleTable();
    $this->blank = blankTable();
});

it('can configure a search name globally', function () {
    Table::useSearchName('test');
    expect(Table::getDefaultSearchName())->toBe('test');
    expect($this->table->getSearchName())->toBe('test');
});

it('can configure whether to use scout globally', function () {
    Table::useScout(true);
    expect(Table::usesScout())->toBeTrue();
    expect($this->table->isScoutSearch())->toBeTrue();

    Table::useScout(false);
    expect(Table::usesScout())->toBeFalse();
});
