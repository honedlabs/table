<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\EmptyState;

trait HasEmptyState
{
    /**
     * The empty state of the table.
     *
     * @var \Honed\Table\EmptyState|null
     */
    protected $emptyState;

    /**
     * Get the empty state of the table.
     *
     * @return \Honed\Table\EmptyState
     */
    public function getEmptyState()
    {
        return $this->emptyState ??= EmptyState::make();
    }
}
