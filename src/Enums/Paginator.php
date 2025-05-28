<?php

namespace Honed\Table\Enums;

enum Paginator: string
{
    case LengthAware = 'length-aware';
    case Simple = 'simple';
    case Cursor = 'cursor';
    case Collection = 'collection';

    public static function coalesce($value)
    {
        return match ($value) {
            'length-aware' => static::LengthAware,
            'simple' => static::Simple,
            'cursor' => static::Cursor,
            'collection' => static::Collection,
            default => null,
        };
    }
}
