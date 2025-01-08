<?php

use Honed\Table\Filters\Enums\Operator;

it('has a label', function () {
    expect(Operator::Equal->label())->toBe('Equal to');
});

it('has a value', function () {
    expect(Operator::Equal->value())->toBe('=');
});

it('has case labels', function () {
    expect(Operator::Equal->label())->toBe('Equal to');
    expect(Operator::NotEqual->label())->toBe('Not equal to');
    expect(Operator::GreaterThan->label())->toBe('Greater than');
    expect(Operator::GreaterThanOrEqual->label())->toBe('Greater than or equal to');
    expect(Operator::LessThan->label())->toBe('Less than');
    expect(Operator::LessThanOrEqual->label())->toBe('Less than or equal to');
    expect(Operator::Like->label())->toBe('Contains');
});
