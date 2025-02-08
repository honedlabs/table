<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasFormatter;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasPlaceholder;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\IsHidden;
use Honed\Core\Concerns\IsKey;
use Honed\Core\Concerns\Transformable;
use Honed\Core\Primitive;

class Column extends Primitive
{
    use Allowable;
    use Concerns\HasBreakpoint;
    use Concerns\HasTooltip;
    use Concerns\IsSearchable;
    use Concerns\IsSortable;
    use Concerns\IsSrOnly;
    use Concerns\IsToggleable;
    use HasExtra;
    use HasFormatter;
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasPlaceholder;
    use IsActive;
    use IsHidden;
    use IsKey;
    use Transformable;

    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct();

        $this->name($name);
        $this->label($label ?? $this->makeLabel($name));
    }

    public function setUp(): void
    {
        $this->active(true);
    }

    public static function make(string $name, ?string $label = null): static
    {
        return resolve(static::class, \compact('name', 'label'));
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
            // 'icon' =>
            'toggle' => $this->isToggleable(),
            'active' => $this->isActive(),
            'sort' => ($this->isSortable() ? [
                'direction' => $this->getSort()?->getDirection(),
                'next' => $this->getSort()?->getNextDirection(),
            ] : null),
            'meta' => $this->getMeta(),
        ];
    }
}
