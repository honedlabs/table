<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Core\Primitive;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\HasAlias;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Table\Contracts\Filters;
use Illuminate\Support\Stringable;
use Honed\Core\Concerns\Validatable;
use Honed\Core\Concerns\Authorizable;
use Honed\Core\Concerns\HasAttribute;
use Honed\Core\Concerns\Transformable;

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
     */
    public function __construct(string $attribute, string $label = null)
    {
        parent::__construct();
        $this->setAttribute($attribute);
        $this->setLabel($label ?? $this->makeLabel($this->getAttribute()));
    }

    /**
     * Make a filter specifying the database column, and optionally the display label.
     */
    public static function make(string $attribute, string $label = null): static
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
        return $this->getAlias() ?? (new Stringable($this->getAttribute()))->afterLast('.')->value();
    }

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
