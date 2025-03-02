<?php

declare(strict_types=1);

use Honed\Table\PerPageRecord;

it('makes', function () {
    expect(PerPageRecord::make(10))
        ->toBeInstanceOf(PerPageRecord::class)
        ->getValue()->toBe(10)
        ->isActive()->toBeFalse();
});

it('has array representation', function () {
    expect(PerPageRecord::make(10, 10)->toArray())
        ->toEqual([
            'value' => 10,
            'active' => true,
        ]);
});
