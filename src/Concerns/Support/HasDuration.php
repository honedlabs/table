<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

trait HasDuration
{
    /**
     * The duration of the cookie to use for remembering the user's preferences.
     *
     * @var int|null
     */
    protected $duration;

    /**
     * Set the duration of the cookie to use for remembering the user's preferences.
     *
     * @return $this
     */
    public function duration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get the duration of the cookie to use for how long the table should.
     * remember the user's preferences.
     */
    public function getDuration(): int
    {
        if (isset($this->duration)) {
            return $this->duration;
        }

        /** @var int */
        return config('table.toggle.remember.duration', 15768000);
    }
}
