<?php

declare(strict_types=1);

namespace Honed\Table\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ViewDeleted
{
    use Dispatchable;

    /**
     * Create a new view deleted event.
     */
    public function __construct(
        public string $table,
        public string $name,
        public string $scope,
    ) {}
}
