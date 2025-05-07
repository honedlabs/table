<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

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
