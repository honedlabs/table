<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Illuminate\Http\Request;
use Honed\Core\Options\Option;
use Honed\Core\Concerns\IsStrict;
use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Illuminate\Database\Eloquent\Builder;
use Honed\Core\Options\Concerns\HasOptions;

class SetFilter extends Filter
{
    use HasOptions;
    use IsStrict;

    /**
     * @var bool
     */
    protected $multiple = false;

    public function setUp(): void
    {
        parent::setUp();
        $this->setType('filter:set');
    }

    public function isFiltering(mixed $value): bool
    {
        if (\is_null($value)) {
            return false;
        }

        if (! $this->hasOptions()) {
            return true;
        }

        $filtering = $this->collectOptions()->reduce(
            static fn (bool $filtering, Option $option) => $option
                ->active(\in_array($option->getValue(), (array) $value))
                ->isActive() || $filtering
            , false);

        return ! $this->isStrict() || $filtering;
    }

    public function getValueFromRequest(Request $request = null): mixed
    {
        $input = ($request ?? request())
            ->input($this->getParameterName(), null);

        return ! \is_null($input) && $this->isMultiple()
            ? \str_getcsv((string) $input)
            : $input;
    }

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'options' => $this->getOptions(),
            'multiple' => $this->isMultiple(),
        ]);
    }

    /**
     * Set as multiple, chainable.
     */
    public function multiple(bool $multiple = true): static
    {
        $this->setMultiple($multiple);

        return $this;
    }

    /**
     * Set as multiple quietly.
     */
    public function setMultiple(bool $multiple): void
    {
        $this->multiple = $multiple;

        !$this->getClause()?->isMultiple()
            && $this->setClause(Clause::Contains);
    }

    /**
     * Determine if it is multiple.
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }
}
