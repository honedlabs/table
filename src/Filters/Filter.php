<?php

namespace Conquest\Table\Filters;

use Closure;
use Conquest\Table\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Conquest\Table\Filters\Concerns\HasClause;
use Conquest\Table\Filters\Concerns\HasOperator;
use Conquest\Table\Filters\Concerns\IsNegatable;
use Conquest\Table\Filters\Enums\Clause;
use Conquest\Table\Filters\Enums\Operator;

class Filter extends BaseFilter
{
    use IsNegatable;
    use HasClause;
    use HasOperator;

    public function __construct(
        array|string|Closure $property, 
        string|Closure $name = null,
        string|Closure $label = null,
        bool|Closure $authorize = null,
        string|Clause $clause = Clause::IS,
        string|Operator $operator = Operator::EQUAL,
        bool $negate = false,
    ) {
        parent::__construct($property, $name, $label, $authorize);
        $this->setClause($clause);
        $this->setOperator($operator);
        $this->setNegation($negate);
        $this->setType('filter');
    }

    public static function make(
        array|string|Closure $property, 
        string|Closure $name = null,
        string|Closure $label = null,
        bool|Closure $authorize = null,
        string|Clause $clause = Clause::IS,
        string|Operator $operator = Operator::EQUAL,
        bool $negate = false,
    ): static {
        return resolve(static::class, compact(
            'property', 
            'name', 
            'label', 
            'authorize',
            'clause',
            'operator',
            'negate',
        ));
    }

    public function apply(Builder|QueryBuilder $builder): void
    {
        $request = request();
        $queryValue = $request->query($this->getName());

        $transformedValue = $this->transformUsing($queryValue);
        $this->setValue($transformedValue);
        $this->setActive($this->filtering($request));

        $builder->when(
            $this->isActive() && $this->isValid($transformedValue),
            fn (Builder|QueryBuilder $builder) => $this->getClause()
                ->apply($builder, 
                    $this->getProperty(), 
                    $this->isNegated() ? $this->getOperator()->negate() : $this->getOperator(), 
                    $this->getValue()
                )
        );
    }
}