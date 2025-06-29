<?php

declare(strict_types=1);

namespace Honed\Table\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ViewCreated
{
    use Dispatchable;

    /**
     * Create a new view created event.
     * 
     * @param  array<string, mixed>  $view
     */
    public function __construct(
        public string $table,
        public string $name,
        public string $scope,
        public array $view,
    ) {}
}
