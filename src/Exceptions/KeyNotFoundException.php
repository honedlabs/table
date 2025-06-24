<?php

declare(strict_types=1);

namespace Honed\Table\Exceptions;

use Exception;
use Honed\Table\Table;

class KeyNotFoundException extends Exception
{
    /**
     * Create a new key not found exception.
     *
     * @param  Table|class-string<Table>  $table
     */
    public function __construct($table)
    {
        $table = $table instanceof Table ? $table::class : $table;

        parent::__construct(
            "The table {$table} must have a key column or a key property defined.",
        );
    }

    /**
     * Throw a new key not found exception.
     *
     * @param  class-string<Table>  $table
     * @return never
     *
     * @throws KeyNotFoundException
     */
    public static function throw($table)
    {
        throw new self($table);
    }
}
