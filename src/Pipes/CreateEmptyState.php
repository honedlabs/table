<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Closure;
use Honed\Core\Pipe;
use Honed\Table\EmptyState;
use Honed\Table\Table;

/**
 * @extends Pipe<\Honed\Table\Table>
 */
class CreateEmptyState extends Pipe
{
    /**
     * Run the create empty state logic.
     */
    public function run(Table $instance): void
    {
        if (! $instance->isEmpty()) {
            $instance->emptyState(null);

            return;
        }

        $emptyState = $instance->getEmptyState() ?? EmptyState::make();

        $instance->emptyState($emptyState);

        $callback = $this->resolveCallback($instance, $emptyState);

        if ($callback) {
            $instance->evaluate($callback);
        }
    }

    /**
     * Resolve the appropriate callback for the current table state.
     *
     * @return ?Closure
     */
    protected function resolveCallback(Table $instance, EmptyState $emptyState): ?Closure
    {
        $states = [
            'searching' => $instance->isSearching(),
            'filtering' => $instance->isFiltering(),
            'refining' => $instance->isFiltering() || $instance->isSearching(),
        ];

        foreach ($states as $type => $isActive) {
            if ($isActive) {
                $callback = match ($type) {
                    'filtering' => $emptyState->getFilteringCallback(),
                    'searching' => $emptyState->getSearchingCallback(),
                    'refining' => $emptyState->getRefiningCallback(),
                };

                if ($callback) {
                    return $callback;
                }
            }
        }

        return null;
    }
}
