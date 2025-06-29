<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @template TClass of \Honed\Table\Table
 *
 * @extends Pipe<TClass>
 */
class Select extends Pipe
{
    /**
     * Run the select logic.
     */
    public function run(): void
    {
        $instance = $this->instance;

        if (! $instance->isSelectable()) {
            return;
        }

        $selects = array_unique($instance->getSelects(), SORT_STRING);

        $resource = $instance->getBuilder();

        if (empty($selects)) {
            $selects = ['*'];
        }

        $resource->select($selects);

        // match (true) {
        //     $resource instanceof Builder => $resource->select($selects),
        //     default => Arr::select($resource, $selects)
        // };
    }
}
