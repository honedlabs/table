<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Illuminate\Database\Eloquent\Builder;

class Filter extends BaseFilter
{
    use Concerns\HasClause;
    use Concerns\HasOperator;

    public function setUp(): void
    {
        $this->setType('filter');
        $this->setClause(Clause::Is);
        $this->setOperator(Operator::Equal);
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
