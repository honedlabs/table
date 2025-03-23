<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\Columns\Column;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Contracts\ShouldSelect;

beforeEach(function () {
    $this->table = Table::make();
});

it('is select', function () {
    expect($this->table)
        ->isSelectable()->toBe(config('table.select'))
        ->select(true)->toBe($this->table)
        ->isSelectable()->toBe(true)
        ->isSelectableByDefault()->toBe(config('table.select'));

    $class = new class extends Table implements ShouldSelect {
        public function __construct() {}
    };

    expect($class)
        ->isSelectable()->toBe(true)
        ->select(false)->toBe($class)
        ->isSelectable()->toBe(false);
});

it('selects columns', function () {
    expect($this->table)
        ->getSelects()->toBeEmpty()
        ->selects('name')->toBe($this->table)
        ->getSelects()->toBe(['name'])
        ->selects(['description', 'price'])->toBe($this->table)
        ->getSelects()->toBe(['name', 'description', 'price']);
});
