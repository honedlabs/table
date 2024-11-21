<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Table\Filters\Concerns\HasClause;
use Honed\Table\Filters\Concerns\HasOperator;
use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Filter extends PropertyFilter
{
    use HasClause;
    use HasOperator;

    public function setUp(): void
    {
        $this->setClause(Clause::Is);
        $this->setOperator(Operator::Equal);
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
