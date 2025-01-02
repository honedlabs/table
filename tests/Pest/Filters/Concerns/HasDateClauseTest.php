<?php

use Honed\Table\Filters\DateFilter;
use Honed\Table\Filters\Enums\DateClause;

beforeEach(function () {
    $this->filter = DateFilter::make('created_at');
});

it('can set a date clause', function () {
    expect($this->filter->clause(DateClause::Day))->toBeInstanceOf(DateFilter::class)
        ->getClause()->toBe(DateClause::Day);
});

it('can be set using setter', function () {
    $this->filter->setClause(DateClause::Day);
    expect($this->filter->getClause())->toBe(DateClause::Day);
});

it('does not accept null values', function () {
    expect($this->filter->getClause())->toBe(DateClause::Date);
    $this->filter->setClause(null);
    expect($this->filter->getClause())->toBe(DateClause::Date);
});

it('checks if it has a clause', function () {
    expect($this->filter->hasClause())->toBeTrue();
});

it('has shorthand for `date`', function () {
    expect($this->filter->date())->toBeInstanceOf(DateFilter::class)
        ->getClause()->toBe(DateClause::Date);
});

it('has shorthand for `day`', function () {
    expect($this->filter->day())->toBeInstanceOf(DateFilter::class)
        ->getClause()->toBe(DateClause::Day);
});

it('has shorthand for `month`', function () {
    expect($this->filter->month())->toBeInstanceOf(DateFilter::class)
        ->getClause()->toBe(DateClause::Month);
});

it('has shorthand for `year`', function () {
    expect($this->filter->year())->toBeInstanceOf(DateFilter::class)
        ->getClause()->toBe(DateClause::Year);
});

it('has shorthand for `time`', function () {
    expect($this->filter->time())->toBeInstanceOf(DateFilter::class)
        ->getClause()->toBe(DateClause::Time);
});
