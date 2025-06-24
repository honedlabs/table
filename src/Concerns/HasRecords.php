<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasRecords
{
    /**
     * The records.
     *
     * @var array<int,array<string,mixed>|\Illuminate\Database\Eloquent\Model>
     */
    protected $records = [];

    /**
     * The metadata associated with record pagination.
     *
     * @var array<string,mixed>
     */
    protected $pagination = [];

    /**
     * Set the records.
     *
     * @param  array<int,array<string,mixed>|\Illuminate\Database\Eloquent\Model>  $records
     * @return void
     */
    public function setRecords($records)
    {
        $this->records = $records;
    }

    /**
     * Get the records.
     *
     * @return array<int,array<string,mixed>|\Illuminate\Database\Eloquent\Model>
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set the pagination metadata.
     *
     * @param  array<string,mixed>  $pagination
     * @return void
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * Get the pagination metadata.
     *
     * @return array<string,mixed>
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}
