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
        if ($this->instance->isNotSelectable()) {
            return;
        }

        $selects = array_unique($this->instance->getSelects(), SORT_STRING);

        $resource = $this->instance->getBuilder();

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
