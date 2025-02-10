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
    use Concerns\HasBreakpoint;
    use Concerns\IsSearchable;
    use Concerns\IsSortable;
    use Concerns\IsSrOnly;
    use Concerns\IsToggleable;
    use HasExtra;
    use HasFormatter;
    use HasIcon;
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasPlaceholder;
    use IsActive;
    use IsHidden;
    use IsKey;
    use Transformable;

    public static function make(string $name, ?string $label = null): static
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    public function setUp(): void
    {
        $this->active(true);
    }

    public function apply(mixed $value): mixed
    {
        $value = $this->transform($value);

        return $this->formatValue($value);
    }

    public function formatValue(mixed $value): mixed
    {
        return $this->format($value) ?? $this->getPlaceholder();
    }

    /**
     * Get the column state as an array
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'hidden' => $this->isHidden(),
            'icon' => $this->getIcon(),
            'toggle' => $this->isToggleable(),
            'active' => $this->isActive(),
            'sort' => $this->isSortable() ? $this->sortToArray() : null,
            'meta' => $this->getMeta(),
        ];
    }
}
