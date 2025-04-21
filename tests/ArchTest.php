<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;

arch('does not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();