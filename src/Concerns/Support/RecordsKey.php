<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

trait RecordsKey
{
    /**
     * The query parameter for the number of records to show per page.
     *
     * @var string|null
     */
    protected $recordsKey;

    /**
     * Set the query parameter for the number of records to show per page.
     *
     * @return $this
     */
    public function recordsKey(string $recordsKey): static
    {
        $this->recordsKey = $recordsKey;

        return $this;
    }

    /**
     * Get the query parameter for the number of records to show per page.
     */
    public function getRecordsKey(): string
    {
        if (isset($this->recordsKey)) {
            return $this->recordsKey;
        }

        return type(config('table.keys.records', 'rows'))->asString();
    }
}
