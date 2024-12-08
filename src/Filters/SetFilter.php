<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Core\Options\Concerns\HasOptions;
use Honed\Table\Filters\Concerns\HasOperator;
use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Illuminate\Database\Eloquent\Builder;

class SetFilter extends BaseFilter
{
    use Concerns\IsMultiple;
    use Concerns\OnlyStrictValues;
    use Concerns\HasOperator;
    use Concerns\HasClause;
    use HasOptions;

    public function setUp(): void
    {
        $this->setType('filter:set');
        $this->setClause(Clause::Is);
        $this->setOperator(Operator::Equal);
    }

    public function apply(Builder $builder): void
    {
        $value = $this->applyTransform($this->getValueFromRequest());
        $this->setValue($value);
        $this->setActive($this->isFiltering($value));

        $builder->when(
            $this->isActive() && $this->applyValidation($value),
            fn (Builder $builder) => $this->handle($builder),
        );
    }

    /**
     * Determine if the filter should be applied.
     * 
     * @return bool
     */
    public function isFiltering(mixed $value): bool
    {
        if (\is_null($value)) {
            return false;
        }

        // Check if the value is in the options
        $isFiltering = false;

        foreach ($this->getOptions() as $option) {
            if ($option->getValue() === $value) {
                $option->setActive(true);
                $isFiltering = true;
            } else {
                $option->setActive(false);
            }
        }

        // If it not strict about the values, then filtering is true
        return $isFiltering || $this->allowsAllValues();
    }

    /**
     * Retrieve the value of the filter name from the current request.
     *
     * @return int|string|array<int,int|string>|null
     */
    public function getValueFromRequest(): mixed
    {
        $input = request()->input($this->getParameterName(), null);
        if (!\is_null($input) && $this->isMultiple()) {
            return \str_getcsv($input);
        }

        return $input;
    }

    public function handle(Builder $builder): void
    {
        match ($this->isMultiple()) {
            true => $builder->whereIn($this->getAttribute(), $this->getValue()),
            false => $this->getClause()->apply($builder, $this->getAttribute(), $this->getOperator(), $this->getValue()),
        };
    }

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'options' => $this->getOptions()
        ]);
    }
}
