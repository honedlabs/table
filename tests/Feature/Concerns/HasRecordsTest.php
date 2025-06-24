<?php

declare(strict_types=1);

use Honed\Table\Table;

beforeEach(function () {
    $this->table = Table::make();
});

it('has records', function () {
    expect($this->table)
        ->getRecords()->toBeEmpty()
        ->setRecords([['key' => 'value']])->toBeNull()
        ->getRecords()->toBe([['key' => 'value']]);
});

it('has pagination', function () {
    expect($this->table)
        ->getPagination()->toBeEmpty()
        ->setPagination(['key' => 'value'])->toBeNull()
        ->getPagination()->toBe(['key' => 'value']);
});
