<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

trait CanBeKey
{
    /**
     * Whether the column is a key.
     *
     * @var bool
     */
    protected $key = false;

    /**
     * Set the column to be a key.
     *
     * @param  bool  $key
     * @return $this
     */
    public function key($key = true)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Whether the column is a key.
     *
     * @return bool
     */
    public function isKey()
    {
        return $this->key;
    }
}
