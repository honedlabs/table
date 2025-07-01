<?php

declare(strict_types=1);

use Honed\Table\Exporters\EloquentExporter;
use Honed\Table\Exporters\Exporter;
use Honed\Table\Operations\Export;
use Honed\Table\Table;
use Maatwebsite\Excel\Excel as ExcelClass;
use Maatwebsite\Excel\Facades\Excel;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->export = Export::make('export')
        ->fileName('products.xlsx')
        ->fileType(ExcelClass::XLSX);

    $this->table = Table::make()
        ->for(Product::class)
        ->operation($this->export);

    Excel::fake();
});

it('sets callback', function () {
    expect($this->export)
        ->getUsingCallback()->toBeNull()
        ->using(fn () => 'test')->toBe($this->export)
        ->getUsingCallback()->toBeInstanceof(Closure::class);
});

it('sets exporter', function () {
    expect($this->export)
        ->getExporter($this->table)->toBe(EloquentExporter::class)
        ->exporter(Product::class)->toBe($this->export)
        ->getExporter($this->table)->toBe(Product::class);
});

it('handles action via download', function () {
    $this->export->download()->handle($this->table);

    Excel::assertDownloaded('products.xlsx');
});

it('handles action via store', function () {
    $this->export->store()->handle($this->table);

    Excel::assertStored('products.xlsx');
});

it('handles action via queue', function () {
    $this->export->queue()->handle($this->table);

    Excel::assertQueued('products.xlsx');
});

it('handles action with callback', function () {
    $this->export->using(
        fn (Exporter $export) => Excel::store(
            $export,
            'callback.xlsx'
        )
    )->handle($this->table);

    Excel::assertStored('callback.xlsx');
});
