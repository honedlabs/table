<?php

namespace Honed\Table\Filters\Enums;

use Illuminate\Database\Eloquent\Builder;

enum Clause: string
{
    case Is = 'is';
    case IsNot = 'is_not';
    case StartsWith = 'starts_with';
    case EndsWith = 'ends_with';
    case Contains = 'contains';
    case DoesNotContain = 'does_not_contain';
    case Json = 'json_contains';
    case NotJson = 'json_does_not_contain';
    case JsonLength = 'json_length';
    case JsonKey = 'json_key';
    case JsonNotKey = 'json_not_key';
    case JsonOverlaps = 'json_overlaps';
    case JsonDoesNotOverlap = 'json_doesnt_overlap';
    case FullText = 'fulltext';
    case Like = 'like';

    public function statement(): string
    {
        return match ($this) {
            self::Is => 'where',
            self::IsNot => 'whereNot',
            self::StartsWith => 'where',
            self::EndsWith => 'where',
            self::Contains => 'whereIn',
            self::DoesNotContain => 'whereNotIn',
            self::Json => 'whereJsonContains',
            self::NotJson => 'whereJsonDoesntContain',
            self::JsonLength => 'whereJsonLength',
            self::JsonKey => 'whereJsonContainsKey',
            self::JsonNotKey => 'whereJsonDoesntContainKey',
            self::JsonOverlaps => 'whereJsonOverlaps',
            self::JsonDoesNotOverlap => 'whereJsonDoesntOverlap',
            self::FullText => 'whereFullText',
            self::Like => 'where',
        };
    }

    public function needsOperator(): bool
    {
        return match ($this) {
            self::JsonLength,
            self::JsonKey,
            self::JsonNotKey,
            self::JsonOverlaps,
            self::JsonDoesNotOverlap,
            self::FullText,
            self::Contains,
            self::DoesNotContain => false,
            default => true,
        };
    }

    public function isMultiple(): bool
    {
        return match ($this) {
            self::Contains, self::DoesNotContain, self::Json, self::NotJson => true,
            default => false,
        };
    }

    public function overrideOperator(Operator $operator): Operator
    {
        return match ($this) {
            self::StartsWith, self::EndsWith, self::Like => Operator::Like,
            default => $operator,
        };
    }

    public function formatValue(mixed $value): mixed
    {
        if ($this->isMultiple()) {
            return is_array($value) ? $value : [$value];
        }

        return match ($this) {
            self::StartsWith => "$value%",
            self::EndsWith => "%$value",
            self::Like => '%'.strtolower($value).'%',
            default => $value,
        };
    }

    public function apply(Builder $builder, string $attribute, Operator $operator, mixed $value): void
    {
        $operator = $this->overrideOperator($operator);

        if ($operator->invalid($value)) {
            return;
        }

        if ($this->needsOperator()) {
            $builder->{$this->statement()}(
                $attribute,
                $operator->value(),
                $this->formatValue($value)
            );

            return;
        }

        $builder->{$this->statement()}(
            $attribute,
            $this->formatValue($value)
        );
    }
}
