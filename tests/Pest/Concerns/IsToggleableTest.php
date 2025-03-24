<?php

use Honed\Table\Table;
use Honed\Table\Columns\Column;
use Honed\Table\Contracts\ShouldToggle;
use Illuminate\Support\Facades\Request;
use Honed\Table\Contracts\ShouldRemember;
use Illuminate\Support\Facades\Cookie;

beforeEach(function () {
    $this->table = Table::make();
});

it('is toggleable', function () {
    // Class-based
    expect($this->table)
        ->isToggleable()->toBe(config('table.toggle'))
        ->toggle(true)->toBe($this->table)
        ->isToggleable()->toBe(true)
        ->isToggleableByDefault()->toBe(config('table.toggle'));

    $class = new class extends Table implements ShouldToggle {
        public function __construct() {}
    };

    expect($class)
        ->isToggleable()->toBe(true)
        ->toggle(false)->toBe($class)
        ->isToggleable()->toBe(false);
});

it('has columns key', function () {
    expect($this->table)
        ->getColumnKey()->toBe(config('table.column_key'))
        ->columnKey('test')
        ->getColumnKey()->toBe('test')
        ->getDefaultColumnKey()->toBe(config('table.column_key'));
});

it('can remember', function () {
    // Class-based
    expect($this->table)
        ->isRememberable()->toBe(config('table.remember'))
        ->remember(true)->toBe($this->table)
        ->isRememberable()->toBe(true)
        ->isRememberableByDefault()->toBe(config('table.remember'));

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
    expect($this->table)
        ->getCookieName()->toBe($this->table->guessCookieName())
        ->cookieName('test')->toBe($this->table)
        ->getCookieName()->toBe('test');
});

it('has duration', function () {
    expect($this->table)
        ->getDuration()->toBe(config('table.duration'))
        ->duration(100)->toBe($this->table)
        ->getDuration()->toBe(100)
        ->getDefaultDuration()->toBe(config('table.duration'));
});

