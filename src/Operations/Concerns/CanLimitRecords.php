<?php

declare(strict_types=1);

namespace Honed\Table\Operations\Concerns;

trait CanLimitRecords
{
    /**
     * Whether to display the export as a page operation.
     *
     * @var bool
     */
    protected $page = true;

    /**
     * Whether to display the export as a bulk operation.
     *
     * @var bool
     */
    protected $bulk = false;

    /**
     * Whether to only use records that have been filtered.
     *
     * @var bool
     */
    protected $filtered = false;

    /**
     * Whether to only use records that have been selected.
     *
     * @var bool
     */
    protected $selected = false;

    /**
     * Limit the export to only the filtered rows. This will execute part of the
     * refining pipeline.
     *
     * @return $this
     */
    public function limitToFilteredRows(bool $value = true): static
    {
        $this->filtered = $value;

        return $this;
    }

    /**
     * Determine if the export is limited to filtered rows.
     */
    public function isLimitedToFilteredRows(): bool
    {
        return $this->filtered;
    }

    /**
     * Limit the export to only the filtered rows. This will execute part of the
     * refining pipeline.
     *
     * @return $this
     */
    public function limitToSelectedRows(bool $value = true): static
    {
        $this->selected = $value;

        return $this->bulk();
    }

    /**
     * Determine if the export is limited to selected rows.
     */
    public function isLimitedToSelectedRows(): bool
    {
        return $this->selected;
    }

    /**
     * Set the export to be a page operation.
     *
     * @param  bool  $value
     * @return $this
     */
    public function page($value = true)
    {
        $this->page = $value;

        return $this;
    }

    /**
     * Determine if the export is a page operation.
     */
    public function isPage(): bool
    {
        return $this->page;
    }

    /**
     * Set the export to be a bulk operation.
     *
     * @param  bool  $value
     * @return $this
     */
    public function bulk($value = true)
    {
        $this->bulk = $value;

        return $this;
    }

    /**
     * Determine if the export is a bulk operation.
     */
    public function isBulk(): bool
    {
        return $this->bulk;
    }
}
