<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Pipelines\CleanupTable;
use Honed\Table\Table;

beforeEach(function () {
    $this->pipe = new CleanupTable();
    $this->next = fn ($table) => $table;
    $this->table = Table::make();
});

it('flushes cached', function () {
    $this->table->cacheColumns([Column::make('name')]);

    expect($this->table->getCachedColumns())->toHaveCount(1);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getCachedColumns())->toBeEmpty();
});
