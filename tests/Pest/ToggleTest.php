<?php

use Honed\Table\Table;
use Honed\Table\Contracts\ShouldToggle;
use Honed\Table\Contracts\ShouldRemember;
use Honed\Table\Tests\Fixtures\Table as FixtureTable;

beforeEach(function () {
    $this->table = FixtureTable::make();
});

it('can toggle', function () {
    // Class-based
    expect($this->table)
        ->isToggleable()->toBe(FixtureTable::Toggle)
        ->toggleable(false)->toBe($this->table)
        ->isToggleable()->toBe(false);

    // Anonymous
    expect(Table::make())
        ->isToggleable()->toBe(config('table.toggle.enabled'))
        ->toggleable(true)->toBeInstanceOf(Table::class)
        ->isToggleable()->toBe(true);

    // Via interface
    $class = new class extends Table implements ShouldToggle {
        public function __construct() {}
    };

    expect($class)
        ->isToggleable()->toBe(true)
        ->toggleable(false)->toBe($class)
        ->isToggleable()->toBe(false);
});

it('has columns key', function () {
    $columnsKey = 'columns';

    // Class-based
    expect($this->table)
        ->getColumnsKey()->toBe(FixtureTable::ColumnsKey)
        ->columnsKey($columnsKey)
        ->getColumnsKey()->toBe($columnsKey);

    // Anonymous
    expect(Table::make())
        ->getColumnsKey()->toBe(config('table.config.columns'))
        ->columnsKey($columnsKey)
        ->getColumnsKey()->toBe($columnsKey);
});

it('can remember', function () {
    // Class-based
    expect($this->table)
        ->isRememberable()->toBe(FixtureTable::Remember)
        ->remember(true)->toBe($this->table)
        ->isRememberable()->toBe(true);

    // Anonymous
    expect(Table::make())
        ->isRememberable()->toBe(config('table.toggle.remember'))
        ->remember(true)->toBeInstanceOf(Table::class)
        ->isRememberable()->toBe(true);

    // Via interface
    $class = new class extends Table implements ShouldRemember {
        public function __construct() {}
    };

    expect($class)
        ->isToggleable()->toBe(true)
        ->isRememberable()->toBe(true)
        ->remember(true)->toBe($class)
        ->isRememberable()->toBe(true);
});

it('has cookie name', function () {
    $cookieName = 'cookie';

    // Class-based
    expect($this->table)
        ->getCookieName()->toBe(FixtureTable::CookieName)
        ->cookieName($cookieName)
        ->getCookieName()->toBe($cookieName);
        
    // Anonymous
    expect(Table::make())
        ->getCookieName()->toBe('table')
        ->cookieName($cookieName)
        ->getCookieName()->toBe($cookieName);
});

it('has duration', function () {
    $duration = 100;

    // Class-based
    expect($this->table)
        ->getDuration()->toBe(FixtureTable::Duration)
        ->duration($duration)
        ->getDuration()->toBe($duration);

    // Anonymous
    expect(Table::make())
        ->getDuration()->toBe(config('table.toggle.duration'))
        ->duration($duration)
        ->getDuration()->toBe($duration);
});