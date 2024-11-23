<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Core\Concerns\HasValue;
use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Illuminate\Database\Eloquent\Builder;


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

    public function apply(Builder $builder): void
    {
        $value = $this->applyTransform($this->getValueFromRequest());
        $this->setValue($value);
        $this->setActive($this->filtering($value));

        $builder->when(
            $this->isActive() && $this->applyValidation($value),
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
