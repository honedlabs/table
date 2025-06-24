<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait InteractsWithDatabase
{
    /**
     * Get the connection name for filter views.
     *
     * @return string
     */
    public function getConnection()
    {
        /** @var string|null */
        $connection = config('table.views.connection');

        /** @var string */
        return ($connection === null || $connection === 'null') ? config('database.default') : $connection;
    }

    /**
     * Get the migration table name for filter views.
     *
     * @return string
     */
    public function getTableName()
    {
        /** @var string|null */
        $table = config('table.views.table');

        /** @var string */
        return ($table === null || $table === 'null') ? 'views' : $table;
    }
}
