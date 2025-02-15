<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasFormatter;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasPlaceholder;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\IsHidden;
use Honed\Core\Concerns\IsKey;
use Honed\Core\Concerns\Transformable;
use Honed\Core\Primitive;

/**
 * @extends Primitive<string, mixed>
 */
class Column extends Primitive
{
    use Allowable;
    use Concerns\HasClass;
    use Concerns\IsSearchable;
    use Concerns\IsSortable;
    use Concerns\IsToggleable;
    use HasExtra;
    use HasFormatter;
    use HasIcon;
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasPlaceholder;
    use HasType;
    use IsActive;
    use IsHidden;
    use IsKey;
    use Transformable;

    /**
     * Create a new column instance.
     */
    public static function make(string $name, ?string $label = null): static
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    public function setUp(): void
    {
        $this->active(true);
        $this->type('default');
    }

    /**
     * Apply the column's transform and format value.
     */
    public function apply(mixed $value): mixed
    {
        $value = $this->transform($value);

        return $this->formatValue($value);
    }

    /**
     * Format the value of the column.
     */
    public function formatValue(mixed $value): mixed
    {
        return $this->format($value) ?? $this->getPlaceholder();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'hidden' => $this->isHidden(),
            'icon' => $this->getIcon(),
            'toggle' => $this->isToggleable(),
            'active' => $this->isActive(),
            'sort' => $this->isSortable() ? $this->sortToArray() : null,
            'class' => $this->getClass(),
            'meta' => $this->getMeta(),
        ];
    }
}
