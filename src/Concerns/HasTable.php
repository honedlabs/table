<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Attributes\Table as TableAttribute;
use Honed\Table\Table;

/**
 * @template TTable of \Honed\Table\Table
 * 
 * @property string $tableClass
 */
trait HasTable
{
    /**
     * Get the table instance for the model.
     *
     * @param  \Closure|null  $before
     * @return TTable
     */
    public static function table($before = null)
    {
        return static::newTable($before) 
            ?? Table::tableForModel(static::class, $before);
    }

    /**
     * Create a new table instance for the model.
     *
     * @param \Closure|null $before
     * @return TTable|null
     */
    protected static function newTable($before = null)
    {
        if (isset(static::$tableClass)) {
            return static::$tableClass::make($before);
        }

        if ($table = static::getTableAttribute()) {
            return $table::make($before);
        }

        return null;
    }

    /**
     * Get the table from the Table class attribute.
     *
     * @return class-string<\Honed\Table\Table>|null
     */
    protected static function getTableAttribute()
    {
        $attributes = (new \ReflectionClass(static::class))
            ->getAttributes(TableAttribute::class);

        if ($attributes !== []) {
            $table = $attributes[0]->newInstance();

            return $table->table;
        }

        return null;
    }
}
