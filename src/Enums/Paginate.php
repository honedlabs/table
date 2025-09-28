<?php

declare(strict_types=1);

namespace Honed\Table\Enums;

enum Paginate: string
{
    case LengthAware = 'length-aware';
    case Simple = 'simple';
    case Cursor = 'cursor';
    case Collection = 'collection';
}
