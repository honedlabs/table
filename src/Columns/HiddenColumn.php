<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Column<TModel, TBuilder>
 */
class HiddenColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    protected $always = true;

    /**
     * {@inheritdoc}
     */
    protected $hidden = true;

    /**
     * {@inheritdoc}
     */
    public function defineType()
    {
        return 'hidden';
    }
}
