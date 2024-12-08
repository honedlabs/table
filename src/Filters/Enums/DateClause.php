<?php

namespace Honed\Table\Filters\Enums;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

enum DateClause: string
{
    case Date = 'date';
    case Day = 'day';
    case Month = 'month';
    case Year = 'year';
    case Time = 'time';

    public function statement(): string
    {
        return match ($this) {
            self::Date => 'whereDate',
            self::Day => 'whereDay',
            self::Month => 'whereMonth',
            self::Year => 'whereYear',
            self::Time => 'whereTime',
        };
    }

    public function formatValue(Carbon|string $value): ?string
    {
        try {
            $value = $value instanceof Carbon ? $value : Carbon::parse($value);
        } catch (\InvalidArgumentException) {
            return null;
        }

        return match ($this) {
            self::Date => $value->toDateString(),
            self::Day => $value->day,
            self::Month => $value->month,
            self::Year => $value->year,
            self::Time => $value->toTimeString(),
        };
    }

    public function apply(Builder $builder, string $property, Operator $operator, Carbon|string $value): void
    {
        $value = $this->formatValue($value);

        if (\is_null($value)) {
            return;
        }

        $builder->{$this->statement()}(
            $property,
            $operator->value(),
            $value
        );
    }
}
