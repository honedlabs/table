<?php

namespace Honed\Table\Concerns;

trait HasMeta
{
    protected array $meta = [];

    /**
     * Set the meta data for the table.
     *
     * @param array<string, mixed>|null $meta
     */
    public function setMeta($meta)
    {
        if (empty($meta)) {
            return;
        }
        $this->meta = $meta;
    }

    /**
     * Get the meta data for the table.
     *
     * @return array<string, mixed>
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Check if the metadata has been set.
     *
     * @return bool
     */
    public function hasMeta()
    {
        return ! $this->missingMeta();
    }

    /**
     * Check if the metadata has not been set.
     *
     * @return bool
     */
    public function missingMeta()
    {
        return empty($this->meta);
    }
}
