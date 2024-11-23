<?php

declare(strict_types=1);

namespace Honed\Table\Sorts;

use Honed\Core\Concerns\Authorizable;
use Honed\Core\Concerns\HasAttribute;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Primitive;
use Honed\Table\Concerns\HasAlias;
use Honed\Table\Contracts\Sorts;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseSort extends Primitive implements Sorts
{
    use Concerns\HasDirection;
    use HasLabel;
    use HasMeta;
    use HasType;
    use IsActive;
    use Authorizable;
    use HasAlias;
    use HasAttribute;

    /**
     * Create a new sort instance specifying the database column, and optionally the display label.
     * 
     * @param string|\Closure():string $attribute
     * @param string|(\Closure():string)|null $label
     */
    final public function __construct(string|\Closure $attribute, string|\Closure|null $label = null)
    {
        parent::__construct();
        $this->setAttribute($attribute);
        $this->setLabel($label ?? $this->makeLabel($this->getAttribute()));
    }

    /**
     * Make a sort specifying the database column, and optionally the display label.
     * 
     * @param string|(\Closure():string) $attribute
     * @param string|(\Closure():string)|null $label
     */
    final public static function make(string|\Closure $attribute, string|\Closure|null $label = null): static
    {
        return resolve(static::class, compact('attribute', 'label'));
    }

    /**
     * Apply the sort to the builder based on the current request.
     * 
     * @param Builder $builder
     * @param string|null $sortBy
     * @param string|null $direction
     */
    public function apply(Builder $builder, ?string $sortBy = null, ?string $direction = null): void
    {
        $this->setActive($this->sorting($sortBy, $direction));

        $builder->when(
            $this->isActive(),
            fn (Builder $builder) => $this->handle($builder, $direction),
        );
    }

    /**
     * Handle the sort by applying the direction to the builder.
     * 
     * @param Builder $builder
     * @param string|null $direction
     */
    public function handle(Builder $builder, ?string $direction = null): void
    {
        $builder->orderBy($builder->qualifyColumn($this->getAttribute()), $direction);
    }

    /**
     * Determine if the sort should be applied.
     * 
     * @param string|null $sortBy
     * @param string|null $direction
     * @return bool
     */
    public function sorting(?string $sortBy, ?string $direction): bool
    {
        return $sortBy === $this->getParameterName();
    }

    /**
     * Retrieve the query parameter name of the sort
     * 
     * @internal
     * @return string
     */
    protected function getParameterName(): string
    {
        return $this->getAlias() ?? str($this->getAttribute())->afterLast('.')->toString();
    }

    /**
     * Get the sort state as an array
     * 
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getParameterName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'meta' => $this->getMeta(),
            'active' => $this->isActive(),
            'direction' => $this->getDirection(),
        ];
    }
}
