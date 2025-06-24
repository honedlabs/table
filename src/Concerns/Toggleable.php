<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Contracts\IsToggleable;

trait Toggleable
{
    public const COLUMN_KEY = 'columns';

    /**
     * Whether the instance supports toggling.
     *
     * @var bool
     */
    protected $toggleable = false;

    /**
     * The query parameter for which columns to display.
     *
     * @var string
     */
    protected $columnKey = self::COLUMN_KEY;

    /**
     * Set the instance to be toggleable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function toggleable($value = true)
    {
        $this->toggleable = $value;

        return $this;
    }

    /**
     * Determine if the instance is toggleable.
     *
     * @return bool
     */
    public function isToggleable()
    {
        return $this->toggleable || $this instanceof IsToggleable;
    }

    /**
     * Set the query parameter for which columns to display.
     *
     * @param  string  $columnKey
     * @return $this
     */
    public function columnKey($columnKey): static
    {
        $this->columnKey = $columnKey;

        return $this;
    }

    /**
     * Get the query parameter for which columns to display.
     *
     * @return string
     */
    public function getColumnKey()
    {
        return $this->columnKey;
    }
}
