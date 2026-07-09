<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Honed\Table\Table;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @extends Pipe<\Honed\Table\Table>
 */
class Select extends Pipe
{
    /**
     * Run the select logic.
     */
    public function run(Table $instance): void
    {
        if ($instance->isNotSelectable()) {
            return;
        }

        $selects = $this->uniqueSelects($instance->getSelects());

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

    /**
     * Ensure the array of strings and DB expressions is unique.
     *
     * @param  array<int, string|Expression>  $selects
     * @return array<int, string|Expression>
     */
    protected function uniqueSelects(array $selects): array
    {
        $seen = [];
        $unique = [];

        foreach ($selects as $select) {
            // DB expressions are assumed to be unique, always include them
            if ($select instanceof Expression) {
                $unique[] = $select;

                continue;
            }

            if (! isset($seen[$select])) {
                $seen[$select] = true;
                $unique[] = $select;
            }
        }

        return $unique;
    }
}
