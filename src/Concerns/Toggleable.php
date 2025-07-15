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
     * @return $this
     */
    public function toggleable(bool $value = true): static
    {
        $this->toggleable = $value;

        return $this;
    }

    /**
     * Set the instance to not be toggleable.
     *
     * @return $this
     */
    public function notToggleable(bool $value = true): static
    {
        return $this->toggleable(! $value);
    }

    /**
     * Determine if the instance is toggleable.
     */
    public function isToggleable(): bool
    {
        return $this->toggleable || $this instanceof IsToggleable;
    }

    /**
     * Determine if the instance is not toggleable.
     */
    public function isNotToggleable(): bool
    {
        return ! $this->isToggleable();
    }

    /**
     * Set the query parameter for which columns to display.
     *
     * @return $this
     */
    public function columnKey(string $columnKey): static
    {
        $this->columnKey = $columnKey;

        return $this;
    }

    /**
     * Get the query parameter for which columns to display.
     */
    public function getColumnKey(): string
    {
        return $this->columnKey;
    }
}
