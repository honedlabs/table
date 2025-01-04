<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Table\Filters\Enums\DateClause;
use Honed\Table\Filters\Enums\Operator;
use Illuminate\Database\Eloquent\Builder;

class DateFilter extends BaseFilter
{
    use Concerns\HasDateClause;
    use Concerns\HasOperator;

    public function setUp(): void
    {
        $this->setType('filter:date');
        $this->setClause(DateClause::Date);
        $this->setOperator(Operator::Equal);
    }

    public function apply(Builder $builder): void
    {
        $value = $this->transform($this->getValueFromRequest());
        $this->setValue($value);
        $this->setActive($this->isFiltering($value));

        $builder->when(
            $this->isActive() && $this->validate($value),
            fn (Builder $builder) => $this->handle($builder),
        );
    }

    public function handle(Builder $builder): void
    {
        $this->getClause()
            ->apply($builder,
                $this->getAttribute(),
                $this->getOperator(),
                $this->getValue()
            );
    }
}
