<?php

declare(strict_types=1);

use Honed\Table\Operations\Export;
use Maatwebsite\Excel\Excel;

beforeEach(function () {
    $this->export = Export::make('export');
});

it('sets file name', function () {
    expect($this->export)
        ->getFileName()->toBe('export.xlsx')
        ->fileName('test')->toBe($this->export)
        ->getFileName()->toBe('test.xlsx')
        ->fileName(fn () => 'test')->toBe($this->export)
        ->getFileName()->toBe('test.xlsx')
        ->fileName('test.pdf')->toBe($this->export)
        ->getFileName()->toBe('test.pdf');
});

it('sets file type', function () {
    expect($this->export)
        ->getFileType()->toBe(Excel::XLSX)
        ->fileType(Excel::CSV)->toBe($this->export)
        ->getFileType()->toBe(Excel::CSV)
        ->getFileName()->toBe('export.csv');
});

it('can be downloaded', function () {
    expect($this->export)
        ->isDownload()->toBeTrue()
        ->download(false)->toBe($this->export)
        ->isDownload()->toBeFalse();
});

it('can be stored', function () {
    expect($this->export)
        ->isStored()->toBeFalse()
        ->store()->toBe($this->export)
        ->isStored()->toBeTrue();
});

it('can be queued', function () {
    expect($this->export)
        ->isQueued()->toBeFalse()
        ->getQueue()->toBeNull()
        ->queue('default')->toBe($this->export)
        ->isQueued()->toBeTrue()
        ->getQueue()->toBe('default');
});

it('can be stored on a disk', function () {
    expect($this->export)
        ->getDisk()->toBeNull()
        ->isStored()->toBeFalse()
        ->disk('s3')->toBe($this->export)
        ->getDisk()->toBe('s3')
        ->isStored()->toBeTrue();
});
