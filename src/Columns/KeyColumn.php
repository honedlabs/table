<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class KeyColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'key';

    /**
     * {@inheritdoc}
     */
    protected $key = true;

    /**
     * {@inheritdoc}
     */
    protected $hidden = true;

    /**
     * {@inheritdoc}
     */
    protected $qualify = true;
}
