<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

class CreateEmptyState
{
    /**
     * Create the empty state of the table considering the refiners, filters and search.
     *
     * @template T of \Honed\Table\Table
     *
     * @param  T  $table
     * @param  \Closure(T):T  $next
     * @return T
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

        $state($emptyState);
    }
}
