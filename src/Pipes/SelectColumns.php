<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Honed\Table\Contracts\Column;
use Honed\Table\Table;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Pipe<\Honed\Table\Table>
 */
class SelectColumns extends Pipe
{
    /**
     * Run the prepare columns logic.
     */
    public function run(Table $instance): void
    {
        $columns = $instance->getHeadings();

        $builder = $instance->getBuilder();

        foreach ($columns as $column) {
            $this->select($instance, $column, $builder);
        }
    }

    /**
     * Select the column.
     *
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $builder
     */
    protected function select(Table $instance, Column $column, Builder $builder): void
    {
        if (! $column->isSelectable()) {
            return;
        }

        $selects = $column->getSelects();

        if (empty($selects)) {
            /** @var string $name */
            $name = $column->getName();

            $selects[] = $name;
        }

        /** @var array<int, string|Expression> $qualifiedSelects */
        $qualifiedSelects = array_map(
            static function (string|Expression $select) use ($column, $builder) {
                /** @var string|Expression */
                return $column->qualifyColumn($select, $builder);
            },
            $selects
        );

        $instance->select($qualifiedSelects);
    }
}
