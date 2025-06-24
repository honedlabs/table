<?php

declare(strict_types=1);

use Honed\Table\Operations\Export;

beforeEach(function () {
    $this->export = Export::make('export');
});

it('limits to filtered rows', function () {
    expect($this->export)
        ->isLimitedToFilteredRows()->toBeFalse()
        ->limitToFilteredRows()->toBe($this->export)
        ->isLimitedToFilteredRows()->toBeTrue();
});

it('limits to selected rows', function () {
    expect($this->export)
        ->isLimitedToSelectedRows()->toBeFalse()
        ->isBulk()->toBeFalse()
        ->limitToSelectedRows()->toBe($this->export)
        ->isLimitedToSelectedRows()->toBeTrue()
        ->isBulk()->toBeTrue();
});

it('sets a page operation', function () {
    expect($this->export)
        ->isPage()->toBeTrue()
        ->page(false)->toBe($this->export)
        ->isPage()->toBeFalse();
});

it('sets as bulk operation', function () {
    expect($this->export)
        ->isBulk()->toBeFalse()
        ->bulk()->toBe($this->export)
        ->isBulk()->toBeTrue();
});
