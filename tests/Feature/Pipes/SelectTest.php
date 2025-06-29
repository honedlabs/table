<?php

declare(strict_types=1);

use Honed\Table\Pipes\Select;
use Honed\Table\Table;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->pipe = new Select();

    $this->table = Table::make()
        ->for(Product::class)
        ->selectable(['id', 'name']);
});

it('selects columns', function () {
    $this->pipe->instance($this->table);

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->columns)
        ->toBeArray()
        ->toHaveCount(2)
        ->toEqualCanonicalizing(['id', 'name']);
});

it('does not select columns if selectable is false', function () {
    $this->pipe->instance($this->table->selectable(false));

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->columns)
        ->toBeNull();
});

it('ensures uniqueness', function () {
    $this->pipe->instance($this->table->select(['id', 'products.id']));

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->columns)
        ->toBeArray()
        ->toHaveCount(3)
        ->toEqualCanonicalizing(['id', 'products.id', 'name']);
});
