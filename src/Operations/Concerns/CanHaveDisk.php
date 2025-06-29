<?php

declare(strict_types=1);

namespace Honed\Table\Operations\Concerns;

trait CanHaveDisk
{
    /**
     * The filesystem disk to be used.
     *
     * @var string|null
     */
    protected $disk;
}
