<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Column<TModel, TBuilder>
 */
class KeyColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->type('key');
        $this->hidden();
        $this->key();
    }
}
