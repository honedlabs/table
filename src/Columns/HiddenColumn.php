<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class HiddenColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'hidden';
    
    /**
     * {@inheritdoc}
     */
    protected $always = true;

    /**
     * {@inheritdoc}
     */
    protected $hidden = true;
}
