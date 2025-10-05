<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

trait CanBeKey
{
    /**
     * Whether the instance is the key.
     *
     * @var bool
     */
    protected $key = false;

    /**
     * Set the instance to be the key.
     *
     * @param  bool  $value
     * @return $this
     */
    public function key($value = true)
    {
        $this->key = $value;

        return $this;
    }

    /**
     * Set the instance to not the key.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notKey($value = true)
    {
        return $this->key(! $value);
    }

    /**
     * Determine if the instance is the key.
     */
    public function isKey(): bool
    {
        return $this->key;
    }

    /**
     * Determine if the instance is not the key.
     *
     * @return bool
     */
    public function isNotKey()
    {
        return ! $this->isKey();
    }
}
