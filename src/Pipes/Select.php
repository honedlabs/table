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
     * Run the after refining logic.
     *
     * @param  TClass  $instance
     * @return void
     */
    public function run($instance)
    {
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
