<?php

declare(strict_types=1);

use Carbon\Carbon;
use Honed\Table\Columns\DateColumn;

beforeEach(function () {
    $this->param = 'created_at';
    $this->column = DateColumn::make($this->param);
});

it('makes', function () {
    expect($this->column)
        ->getName()->toBe($this->param)
        ->getLabel()->toBe('Created at')
        ->getType()->toBe('date');
});

it('is diff for humans', function () {
    expect($this->column)
        ->isDiffForHumans()->toBeFalse()
        ->diffForHumans()->toBe($this->column)
        ->isDiffForHumans()->toBeTrue();
});

it('has format', function () {
    expect($this->column)
        ->getBuildermat()->toBeNull()
        ->format('Y-m-d')->toBe($this->column)
        ->getBuildermat()->toBe('Y-m-d');
});

it('has timezone', function () {
    expect($this->column)
        ->getTimezone()->toBe(config('app.timezone'))
        ->timezone('America/New_York')->toBe($this->column)
        ->getTimezone()->toBe('America/New_York');
});

it('applies', function () {
    $date = Carbon::parse('2000-01-01');

    expect($this->column)
        ->fallback('N/A')->toBe($this->column)
        ->apply(null)->toBe('N/A')
        ->apply('invalid')->toBe('N/A')
        ->format('d M Y')->toBe($this->column)
        ->apply($date->toIso8601String())->toBe('01 Jan 2000')
        ->diffForHumans()->toBe($this->column)
        ->apply($date->toIso8601String())->toBeString();
});
