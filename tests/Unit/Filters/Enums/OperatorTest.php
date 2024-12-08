<?php

use Honed\Table\Filters\Enums\Operator;

it('has a label', function () {
    expect(Operator::Equal->label())->toBe('Equal to');
});

it('has a value', function () {
    expect(Operator::Equal->value())->toBe('=');
});
