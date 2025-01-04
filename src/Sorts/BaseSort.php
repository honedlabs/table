<?php

declare(strict_types=1);

namespace Honed\Table\Sorts;

use Honed\Core\Concerns\Authorizable;
use Honed\Core\Concerns\HasAlias;
use Honed\Core\Concerns\HasAttribute;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Primitive;
use Honed\Table\Contracts\Sorts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Stringable;

abstract class BaseSort extends Primitive implements Sorts
{
    use Authorizable;
    use Concerns\HasDirection;
    use HasAlias;
    use HasAttribute;
    use HasLabel;
    use HasMeta;
    use HasType;
    use IsActive;

    /**
     * Create a new sort instance specifying the database column, and optionally the display label.
     */
    public function __construct(string $attribute, ?string $label = null)
    {
        parent::__construct();
        $this->setAttribute($attribute);
        $this->setLabel($label ?? $this->makeLabel($this->getAttribute()));
    }

    /**
     * Make a sort specifying the database column, and optionally the display label.
     */
    public static function make(string $attribute, ?string $label = null): static
    {
        return resolve(static::class, compact('attribute', 'label'));
    }

    public function apply(Builder $builder, ?string $sortBy, ?string $direction = 'asc'): void
    {
        $this->setActive($this->isSorting($sortBy, $direction));
        $this->setActiveDirection($direction);

        $builder->when(
            $this->isActive(),
            fn (Builder $builder) => $this->handle($builder, $direction),
        );
    }

    public function handle(Builder $builder, ?string $direction = null): void
    {
        $builder->orderBy($this->getAttribute(), $direction ?? static::getDefaultDirection());
    }

    public function isSorting(?string $sortBy, ?string $direction): bool
    {
        return $sortBy === $this->getParameterName();
    }

    public function getParameterName(): string
    {
        return $this->getAlias() ?? (new Stringable($this->getAttribute()))->afterLast('.')->value();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getParameterName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'isActive' => $this->isActive(),
            'meta' => $this->getMeta(),
            ...(! $this->hasDirection() ? ['direction' => $this->getActiveDirection()] : []),
        ];
    }
}
