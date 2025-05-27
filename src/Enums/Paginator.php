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
            'length-aware' => self::LengthAware,
            'simple' => self::Simple,
            'cursor' => self::Cursor,
            'collection' => self::Collection,
            default => null,
        };
    }
}
