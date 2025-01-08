<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Core\Concerns\Authorizable;
use Honed\Core\Concerns\HasAlias;
use Honed\Core\Concerns\HasAttribute;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\Transformable;
use Honed\Core\Concerns\Validatable;
use Honed\Core\Primitive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;

abstract class BaseFilter extends Primitive implements Contracts\Filter
{
    use Authorizable;
    use HasAlias;
    use HasAttribute;
    use HasLabel;
    use HasMeta;
    use HasType;
    use HasValue;
    use IsActive;
    use Transformable;
    use Validatable;

    /**
     * Create a new filter instance specifying the database column, and optionally the display label.
     */
    public function __construct(string $attribute, ?string $label = null)
    {
        parent::__construct();
        $this->setAttribute($attribute);
        $this->setLabel($label ?? $this->makeLabel($attribute));
    }

    /**
     * Make a filter specifying the database column, and optionally the display label.
     */
    public static function make(string $attribute, ?string $label = null): static
    {
        return resolve(static::class, compact('attribute', 'label'));
    }

    public function apply(Builder $builder, ?Request $request = null): void
    {
        $value = $this->transform(
            $this->getValueFromRequest($request)
        );

        $this->setValue($value);

        $this->setActive(
            $this->isFiltering($value)
        );

        $builder->when(
            $this->isActive() && $this->validate($value),
            fn (Builder $builder) => $this->handle($builder),
        );
    }

    public function getValueFromRequest(?Request $request = null): mixed
    {
        return ($request ?? request())
            ->input($this->getParameterName(), null);
    }

    public function isFiltering(mixed $value): bool
    {
        return ! \is_null($value);
    }

    public function getParameterName(): string
    {
        return $this->getAlias()
            ?? (new Stringable($this->getAttribute()))
                ->afterLast('.')
                ->value();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getParameterName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'active' => $this->isActive(),
            'value' => $this->getValue(),
            'meta' => $this->getMeta(),
        ];
    }
}
