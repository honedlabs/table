<?php

namespace Conquest\Table\Pipes;

use Closure;
use Conquest\Table\Actions\InlineAction;
use Conquest\Table\Pipes\Contracts\SetsActions;
use Conquest\Table\Table;

/**
 * @internal
 */
class SetActions implements SetsActions
{
    public function handle(Table $table, Closure $next)
    {
        // Get the inline actions which are authorized
        // Get the records
        // Set the action property
        // Validate each action on record
        return $next($table);
    }

    /**
     * Scope the set actions to ensure memory scope
     * 
     * @param $record
     * @param array<int, InlineAction> $actions
     */
    protected function setActions(mixed $record, array $actions)
    {

    }

    protected function setAction(mixed $record, InlineAction $action)
    {

    }
}
