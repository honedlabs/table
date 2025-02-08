<?php

declare(strict_types=1);

use Honed\Table\PageAmount;

it('can be made', function () {
    expect(PageAmount::make(10))
        ->toBeInstanceOf(PageAmount::class)
        ->getValue()->toBe(10)
        ->isActive()->toBeFalse();
});

it('has array representation', function () {
    expect((new PageAmount(10))->toArray())
        ->toBeArray()
        ->toHaveKeys(['value', 'active']);
});
