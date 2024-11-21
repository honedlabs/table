<?php

namespace Honed\Table\Concerns;

use Illuminate\Support\Collection;

trait Records
{
    /**
     * @var \Illuminate\Support\Collection<array-key, array<array-key, mixed>>|null
     */
    protected ?Collection $records = null;

    /**
     * Set the records for the table.
     * 
     * @param \Illuminate\Support\Collection<array-key, array<array-key, mixed>> $records
     */
    public function setRecords($records): void
    {
        $this->records = $records;
    }
    
    public function getRecords(): ?Collection
    {
        if (! $this->hasRecords()) {
            return null;
        }

        return $this->records;
    }

    public function hasRecords(): bool
    {
        return ! is_null($this->records);
    }
}
