<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Illuminate\Contracts\Database\Query\Expression;
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

        $selects = $this->uniqueSelects($this->instance->getSelects());

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

            // For strings, check for uniqueness
            if (is_string($select) && ! isset($seen[$select])) {
                $seen[$select] = true;
                $unique[] = $select;
            }
        }

        return $unique;
    }
}
