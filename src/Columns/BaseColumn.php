<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Closure;
use Honed\Core\Primitive;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsAuthorized;
use Honed\Core\Concerns\IsKey;
// use Honed\Core\Concerns\HasMeta;
// use Honed\Core\Concerns\HasPlaceholder;
// use Honed\Core\Concerns\IsActive;
// use Honed\Core\Concerns\IsHidden;
// use Honed\Core\Concerns\Transforms;
// use Honed\Table\Columns\Concerns\HasBreakpoint;
// use Honed\Table\Columns\Concerns\HasTooltip;
// use Honed\Table\Columns\Concerns\IsSortable;
// use Honed\Table\Columns\Concerns\IsSrOnly;
// use Honed\Table\Columns\Concerns\IsToggleable;
// use Honed\Table\Table;
// use InvalidArgumentException;

abstract class BaseColumn extends Primitive
{
    // use HasBreakpoint;
    use HasLabel;
    // use HasMeta;
    use HasName;
    // use HasPlaceholder;
    // use HasTooltip;
    use HasType;
    // use IsActive;
    use IsAuthorized;
    // use IsHidden;
    use IsKey;
    // use IsSortable;
    // use IsSrOnly;
    // use IsToggleable;
    // use Transforms;

    final public function __construct(string|Closure $name, string|Closure $label = null)
    {
        parent::__construct();
        $this->setName($name);
        $this->setLabel($label ?? $this->makeLabel($this->getName()));
    }

    final public static function make(string|Closure $name, string|Closure $label = null): static
    {
        return resolve(static::class, compact('name', 'label'));
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'hidden' => $this->isHidden(),
            'placeholder' => $this->getPlaceholder(),
            'tooltip' => $this->getTooltip(),
            'breakpoint' => $this->getBreakpoint(),
            'sr' => $this->isSrOnly(),

            'toggle' => $this->isToggleable(),
            'active' => $this->isToggledOn(),

            'sort' => $this->isSortable(),
            'sorting' => $this->isSorting(),
            'direction' => $this->getSort()?->getDirection(),

            'meta' => $this->getMeta(),
        ];
    }

    public function apply(mixed $value): mixed
    {
        $value = $this->applyTransform($value);

        return $this->formatValue($value);
    }

    /**
     * Format the value to be displayed in the column.
     * 
     * @param mixed $value
     * @return mixed
     */
    public function formatValue(mixed $value): mixed
    {
        return $value;
    }
}
