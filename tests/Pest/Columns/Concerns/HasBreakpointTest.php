<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('has no breakpoint by default', function () {
    expect($this->column->hasBreakpoint())->toBeFalse();
});

it('can set the breakpoint', function () {
    expect($this->column->breakpoint(Column::ExtraSmall))->toBeInstanceOf(Column::class)
        ->getBreakpoint()->toBe(Column::ExtraSmall);
});

it('can be set using setter', function () {
    $this->column->setBreakpoint(Column::ExtraSmall);
    expect($this->column->getBreakpoint())->toBe(Column::ExtraSmall);
});

it('does not accept null values', function () {
    $this->column->setBreakpoint(null);
    expect($this->column->getBreakpoint())->toBeNull();
});

it('has shorthand for xs breakpoint', function () {
    expect($this->column->xs())->toBeInstanceOf(Column::class)
        ->getBreakpoint()->toBe(Column::ExtraSmall);
});

it('has shorthand for sm breakpoint', function () {
    expect($this->column->sm())->toBeInstanceOf(Column::class)
        ->getBreakpoint()->toBe(Column::Small);
});

it('has shorthand for md breakpoint', function () {
    expect($this->column->md())->toBeInstanceOf(Column::class)
        ->getBreakpoint()->toBe(Column::Medium);
});

it('has shorthand for lg breakpoint', function () {
    expect($this->column->lg())->toBeInstanceOf(Column::class)
        ->getBreakpoint()->toBe(Column::Large);
});

it('has shorthand for xl breakpoint', function () {
    expect($this->column->xl())->toBeInstanceOf(Column::class)
        ->getBreakpoint()->toBe(Column::ExtraLarge);
});

it('does not accept invalid breakpoints', function () {
    expect(fn () => $this->column->breakpoint('invalid'))->toThrow(\InvalidArgumentException::class);
});
