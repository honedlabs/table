<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('is exportable', function () {
    expect($this->column)
        ->isExportable()->toBeTrue()
        ->isNotExportable()->toBeFalse()
        ->getExportable()->toBeTrue()
        ->exportable(fn () => 'value')->toBe($this->column)
        ->isExportable()->toBeTrue()
        ->getExportable()->toBeInstanceOf(Closure::class)
        ->notExportable()->toBe($this->column)
        ->isNotExportable()->toBeTrue()
        ->getExportable()->toBeFalse();
});

it('has export style', function () {
    expect($this->column)
        ->getExportStyle()->toBeNull()
        ->exportStyle(['font' => ['bold' => true]])->toBe($this->column)
        ->getExportStyle()->toEqual(['font' => ['bold' => true]]);
});

it('has export format', function () {
    expect($this->column)
        ->getExportFormat()->toBeNull()
        ->exportFormat(NumberFormat::FORMAT_CURRENCY_USD_INTEGER)->toBe($this->column)
        ->getExportFormat()->toBe(NumberFormat::FORMAT_CURRENCY_USD_INTEGER);
})->skip(fn () => app()->version() < '12.0');
