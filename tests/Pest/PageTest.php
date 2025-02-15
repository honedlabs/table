<?php

declare(strict_types=1);

use Honed\Table\Page;

it('can be made', function () {
    expect(Page::make(10))
        ->toBeInstanceOf(Page::class)
        ->getValue()->toBe(10)
        ->isActive()->toBeFalse();
});

it('has array representation', function () {
    expect(Page::make(10, 10)->toArray())
        ->toEqual([
            'value' => 10,
            'active' => true,
        ]);
});
