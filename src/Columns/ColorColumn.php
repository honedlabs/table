<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends \Honed\Infolist\Entries\ColorEntry<TModel, TBuilder>
 */
class ColorColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->type('color');
    }
}
