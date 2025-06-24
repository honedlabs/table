<?php

declare(strict_types=1);

use Illuminate\Console\Command;

arch()->preset()->php();

arch()->preset()->security();

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('strict types')
    ->expect('Honed\Table')
    ->toUseStrictTypes();

arch('attributes')
    ->expect('Honed\Table\Attributes')
    ->toBeClasses();
// ->toExtend(\Attribute::class);

arch('concerns')
    ->expect('Honed\Table\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Table\Contracts')
    ->toBeInterfaces();

arch('commands')
    ->expect('Honed\Table\Console\Commands')
    ->toBeClasses()
    ->toExtend(Command::class);

arch('exceptions')
    ->expect('Honed\Table\Exceptions')
    ->toExtend(Exception::class);
