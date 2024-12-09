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
     *
     * @param  string|\Closure():string  $attribute
     * @param  string|(\Closure():string)|null  $label
     */
    public function __construct(string|\Closure $attribute, string|\Closure|null $label = null)
    {
        parent::__construct();
        $this->setAttribute($attribute);
        $this->setLabel($label ?? $this->makeLabel($this->getAttribute()));
    }

    /**
     * Make a sort specifying the database column, and optionally the display label.
     *
     * @param  string|(\Closure():string)  $attribute
     * @param  string|(\Closure():string)|null  $label
     */
    public static function make(string|\Closure $attribute, string|\Closure|null $label = null): static
    {
        return resolve(static::class, compact('attribute', 'label'));
    }

    public function apply(Builder $builder, string $sortBy, string $direction): void
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
        return $this->getAlias() ?? str($this->getAttribute())->afterLast('.')->toString();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getParameterName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'isActive' => $this->isActive(),
            'meta' => $this->getMeta(),
            ...($this->isAgnostic() ? ['direction' => $this->getActiveDirection()] : []),
        ];
    }
}
