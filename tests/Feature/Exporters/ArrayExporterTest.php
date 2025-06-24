<?php

declare(strict_types=1);

use Honed\Table\Exporters\ArrayExporter;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = ProductTable::make();

    $this->exporter = new ArrayExporter($this->table);
});

it('can export', function () {
    expect($this->exporter->array())
        ->toBeArray();
});
