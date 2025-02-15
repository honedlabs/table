<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

trait CanToggle
{
    /**
     * Whether the table should allow the user to toggle which columns are visible.
     *
     * @var bool|null
     */
    protected $toggle;

    /**
     * Determine whether this table allows for the user to toggle which
     * columns are visible.
     */
    public function canToggle(): bool
    {
        if (isset($this->toggle)) {
            return $this->toggle;
        }

        return (bool) config('table.toggle.enabled', false);
    }
}
