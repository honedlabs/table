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
use Honed\Core\Options\Concerns\HasOptions;
use Honed\Core\Primitive;
use Honed\Table\Contracts\Filters;

abstract class BaseFilter extends Primitive implements Filters
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
     *
     * @param  string|(\Closure():string)  $attribute
     * @param  string|(\Closure():string)|null  $label
     */
    public function __construct(string|\Closure $attribute, string|\Closure|null $label = null)
    {
        parent::__construct();
        $this->setAttribute($attribute);
        $this->setLabel($label ?? $this->makeLabel($this->getAttribute()));
    }

    /**
     * Make a filter specifying the database column, and optionally the display label.
     *
     * @param  string|(\Closure():string)  $attribute
     * @param  string|(\Closure():string)|null  $label
     */
    public static function make(string|\Closure $attribute, string|\Closure|null $label = null): static
    {
        return resolve(static::class, compact('attribute', 'label'));
    }

    public function getValueFromRequest(): mixed
    {
        return request()->input($this->getParameterName(), null);
    }

    public function isFiltering(mixed $value): bool
    {
        return ! \is_null($value);
    }

    public function getParameterName(): string
    {
        return $this->getAlias() ?? str($this->getAttribute())->afterLast('.')->toString();
    }

    /**
     * Get the filter state as an array
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getParameterName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'isActive' => $this->isActive(),
            'value' => $this->getValue(),
            'meta' => $this->getMeta(),
        ];
    }
}
