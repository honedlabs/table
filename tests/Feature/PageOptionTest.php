<?php

declare(strict_types=1);

use Honed\Table\PageOption;

it('makes', function () {
    expect(PageOption::make(10))
        ->toBeInstanceOf(PageOption::class)
        ->getValue()->toBe(10)
        ->isActive()->toBeFalse();
});

it('has array representation', function () {
    expect(PageOption::make(10, 10)->toArray())
        ->toEqual([
            'value' => 10,
            'active' => true,
        ]);
});

it('serializes to json', function () {
    $pageOption = PageOption::make(10, 10);

    expect($pageOption->jsonSerialize())
        ->toEqual($pageOption->toArray());
});
