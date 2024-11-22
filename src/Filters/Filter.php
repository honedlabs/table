<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Core\Concerns\HasValue;
use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;


class Filter extends BaseFilter
{
    use HasValue;
    use Concerns\HasClause;
    use Concerns\HasOperator;

    public function setUp(): void
    {
        $this->setClause(Clause::Is);
        $this->setOperator(Operator::Equal);
    }

    public function apply(Builder|QueryBuilder $builder): void
    {
        $value = $this->applyTransform($this->getValueFromRequest());
        $this->setValue($value);
        $this->setActive($this->filtering($value));

        $builder->when(
            $this->isActive() && $this->applyValidation($value),
            fn (Builder|QueryBuilder $builder) => $this->handle($builder),
        );
    }

    public function handle(Builder|QueryBuilder $builder): void
    {
        $this->getClause()
            ->apply($builder,
                $this->getProperty(),
                $this->getOperator(),
                $this->getValue()
            );
    }
}
