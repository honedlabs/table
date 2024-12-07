<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Closure;
use Honed\Table\Table;
use Honed\Table\Columns\BaseColumn;
use Honed\Table\Pipes\Contracts\Toggles;

/**
 * @internal
 */
class ApplyToggles implements Toggles
{
    public function handle(Table $table, Closure $next)
    {
        // Get the toggled columns from the request
        $toggles = $table->getToggledColumnsTerm();

        // Change each column to the active state based on whether the column is in the toggled columns
        if (! \is_null($toggles)) {
            $table->getColumns()->each(function (BaseColumn $column) use ($toggles) {
                if (\in_array($column->getName(), $toggles)) {
                    $column->setActive(true);
                } else {
                    $column->setActive(false);
                }
            });
        }

        return $next($table);
    }
}
