<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Table\EmptyState;
use Honed\Table\Table;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
 */
class CreateEmptyState
{
    /**
     * Create the empty state of the table considering the refiners, filters and search.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        // Get the empty state and run it through the defaults.
        $state = $table->getEmptyState();
        $table->defineEmptyState($state);

        $isSearching = $table->isSearching();
        $isFiltering = $table->isFiltering();
        $isRefining = $isSearching || $isFiltering;

        if ($isSearching && $searching = $state->getSearchingState()) {
            $this->apply($searching, $state);
        } elseif ($isFiltering && $filtering = $state->getFilteringState()) {
            $this->apply($filtering, $state);
        } elseif ($isRefining && $refining = $state->getRefiningState()) {
            $this->apply($refining, $state);
        }

        return $next($table);
    }

    /**
     * Apply the state to the empty state.
     *
     * @param  string|\Closure  $state
     * @param  \Honed\Table\EmptyState  $emptyState
     * @return void
     */
    protected function apply($state, $emptyState)
    {
        if (\is_string($state)) {
            $emptyState->message($state);

            return;
        }

        // dd($state($emptyState), $state(EmptyState::make()));

        $state($emptyState);
    }
}
