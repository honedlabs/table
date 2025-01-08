<?php

namespace Honed\Table\Tests\Stubs;

enum Status: int
{
    case Available = 0;
    case Unavailable = 1;
    case ComingSoon = 2;

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Unavailable => 'Unavailable',
            self::ComingSoon => 'Coming soon',
        };
    }
}
