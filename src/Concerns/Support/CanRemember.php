<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

use Honed\Table\Contracts\ShouldRemember;

trait CanRemember
{
    /**
     * Whether the table should remember the user's preferences for column visibility
     * via a cookie.
     *
     * @var bool|null
     */
    protected $remember;

    /**
     * Determine whether the user's preferences should be remembered.
     */
    public function canRemember(): bool
    {
        if (isset($this->remember)) {
            return $this->remember;
        }

        if ($this instanceof ShouldRemember) {
            return true;
        }

        return (bool) config('table.toggle.remember', false);
    }
}
