<?php

declare(strict_types=1);

arch('does not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
