<?php

use Honed\Table\Table;

beforeEach(function () {
    Table::useSearchTerm('search');
    Table::useScout(false);

    $this->table = exampleTable();
    $this->blank = blankTable();
});

it('can configure a search name globally', function () {
    Table::useSearchTerm('test');
    expect($this->table->getSearchTerm())->toBe('test');
});

it('can configure whether to use scout globally', function () {
    Table::useScout(true);
    expect(Table::usesScout())->toBeTrue();
    expect($this->table->isScoutSearch())->toBeTrue();

    Table::useScout(false);
    expect(Table::usesScout())->toBeFalse();
});
