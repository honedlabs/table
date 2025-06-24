<?php

declare(strict_types=1);

use Honed\Table\Operations\Export;
use Maatwebsite\Excel\Events\BeforeExport;

beforeEach(function () {
    $this->export = Export::make('export');
});

it('sets events', function () {
    expect($this->export)
        ->getEvents()->toBeEmpty()
        ->events([BeforeExport::class => fn () => 'test'])->toBe($this->export)
        ->getEvents()
        ->scoped(fn ($events) => $events
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(BeforeExport::class)
            ->{BeforeExport::class}->toBeInstanceof(Closure::class)
        );
});

it('sets event', function () {
    expect($this->export)
        ->getEvents()->toBeEmpty()
        ->event(BeforeExport::class, fn () => 'test')->toBe($this->export)
        ->getEvents()
        ->scoped(fn ($events) => $events
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(BeforeExport::class)
            ->{BeforeExport::class}->toBeInstanceof(Closure::class)
        );
});
