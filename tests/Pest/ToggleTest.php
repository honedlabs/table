<?php

use Honed\Table\Table;
use Honed\Table\Tests\Fixtures\Table as FixtureTable;

beforeEach(function () {
    $this->table = FixtureTable::make();
});

it('can set as toggleable', function () {
    expect($this->table)
        ->canToggle()->toBe(FixtureTable::Toggle);
});

it('can set the columns key', function () {
    expect($this->table)
        ->getColumnsKey()->toBe(FixtureTable::ColumnsKey)
        ->columnsKey('test')
        ->getColumnsKey()->toBe('test');

    expect(Table::make())
        ->getColumnsKey()->toBe(config('table.keys.columns'))
        ->columnsKey('test')
        ->getColumnsKey()->toBe('test');
});

it('can set a duration', function () {
    expect($this->table)
        ->getDuration()->toBe(FixtureTable::Duration)
        ->duration(100)
        ->getDuration()->toBe(100);

    expect(Table::make())
        ->getDuration()->toBe(config('table.toggle.duration'))
        ->duration(100)
        ->getDuration()->toBe(100);
});

it('can set as remembering', function () {
    expect($this->table)
        ->canRemember()->toBe(FixtureTable::Remember);
});

it('can set a cookie', function () {
    expect($this->table)
        ->getCookie()->toBe(FixtureTable::Cookie);

    expect($table = Table::make())
        ->getCookie()->toBe($table->guessCookieName());
});

// it('toggles columns', function () {
//     expect($this->table->buildTable())
//         ->getActiveColumns()->dd();

// });
