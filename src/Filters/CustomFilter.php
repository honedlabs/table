<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Illuminate\Database\Eloquent\Builder;

class CustomFilter extends BaseFilter
{
    use Concerns\HasQuery;

    public function setUp(): void
    {
        $this->setType('filter:custom');
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

    public function handle(Builder $builder): void
    {
        if ($this->missingQuery()) {
            return;
        }

        $this->getQuery()($builder);
    }
}
