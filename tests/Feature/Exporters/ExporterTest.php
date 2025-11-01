<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Exporters\EloquentExporter;
use Honed\Table\Table;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel as ExcelClass;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Workbench\App\Models\Product;

beforeEach(function () {
    Excel::fake();

    $this->table = Table::make()
        ->for(Product::class)
        ->columns([
            Column::make('id')->exportable(false),
            Column::make('name')->exportStyle(fn (Style $style) => $style->getFont()->setBold(true)),
            Column::make('price')->exportFormat(NumberFormat::FORMAT_CURRENCY_USD_INTEGER),
        ]);

    $this->export = new EloquentExporter($this->table);
})->skip(fn () => app()->version() < '12.0');

it('can create export', function () {
    Excel::store($this->export, 'products.xlsx', null, ExcelClass::XLSX);

    Excel::assertStored('products.xlsx');
});

it('has headings', function () {
    expect($this->export->headings())
        ->toBeArray()
        ->toHaveCount(2)
        ->toEqual([
            'Name',
            'Price',
        ]);
});

it('maps values', function () {
    $product = Product::factory()->create();

    expect($this->export->map($product))
        ->toBeArray()
        ->toHaveCount(2)
        ->toEqual([
            $product->name,
            $product->price,
        ]);
})->skip();

it('has style sheet', function () {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Write dummy data to the first row
    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Price');

    $this->export->styles($sheet);

    // Now check the style on A1 and B1
    expect($sheet->getStyle('A1')->getFont()->getBold())->toBeTrue();
    expect($sheet->getStyle('B1')->getNumberFormat()->getFormatCode())->toBe(NumberFormat::FORMAT_CURRENCY_USD_INTEGER);
});

it('can register events', function () {
    $this->export->events([
        BeforeExport::class => fn (BeforeExport $event) => $event,
    ]);

    expect($this->export->registerEvents())
        ->toHaveCount(1)
        ->toHaveKey(BeforeExport::class)
        ->{BeforeExport::class}->toBeInstanceof(Closure::class);
});
