<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Illuminate\Http\Request;
use Honed\Table\Filters\Enums\Operator;
use Honed\Table\Filters\Enums\DateClause;
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
