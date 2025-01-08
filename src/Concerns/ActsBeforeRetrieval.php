<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ActsBeforeRetrieval
{
    /**
     * Determine if there is a before retrieval method.
     */
    public function actsBeforeRetrieval(): bool
    {
        return \method_exists($this, 'before');
    }

    /**
     * Apply the before retrieval query to the builder.
     */
    public function beforeRetrieval(Builder $builder): void
    {
        if ($this->actsBeforeRetrieval()) {
            \call_user_func([$this, 'before'], $builder);
        }
    }
}
