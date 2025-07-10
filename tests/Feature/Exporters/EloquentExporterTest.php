<?php

declare(strict_types=1);

use Honed\Table\Exporters\EloquentExporter;
use Illuminate\Database\Eloquent\Builder;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = ProductTable::make();

    $this->table->define(); // @TODO

    $this->exporter = new EloquentExporter($this->table);
});

it('can export', function () {
    expect($this->exporter->query())
        ->toBeInstanceOf(Builder::class);
});
