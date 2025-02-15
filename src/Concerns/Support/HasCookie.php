<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

trait HasCookie
{
    /**
     * The name of the cookie to use for remembering the user's preferences.
     *
     * @var string|null
     */
    protected $cookie;

    /**
     * Get the cookie name to use for the table toggle.
     */
    public function getCookie(): string
    {
        if (isset($this->cookie)) {
            return $this->cookie;
        }

        return $this->guessCookieName();
    }

    /**
     * Guess the name of the cookie to use for the table toggle.
     */
    public function guessCookieName(): string
    {
        return str(static::class)
            ->classBasename()
            ->append('Table')
            ->kebab()
            ->lower()
            ->toString();
    }
}
