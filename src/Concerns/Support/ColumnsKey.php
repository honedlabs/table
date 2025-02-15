<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

trait ColumnsKey
{
    /**
     * The query parameter for which columns to display as a comma-separated list
     * of column names.
     *
     * @var string|null
     */
    protected $columnsKey;

    /**
     * Set the query parameter for which columns to display as a comma-separated
     * list of column names.
     *
     * @return $this
     */
    public function columnsKey(string $columnsKey): static
    {
        $this->columnsKey = $columnsKey;

        return $this;
    }

    /**
     * Get the query parameter for which columns to display as a comma-separated
     * list of column names.
     */
    public function getColumnsKey(): string
    {
        if (isset($this->columnsKey)) {
            return $this->columnsKey;
        }

        /** @var string */
        return config('table.keys.columns', 'columns');
    }
}
