<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait Selectable
{
    /**
     * Whether the records are selectable for bulk actions.
     * 
     * @var bool|(\Closure():bool)
     */
    // protected $selectable;

    /**
     * @var bool|(\Closure():bool)
     */
    protected static $globalSelectable = true;

    /**
     * Set the selectable for bulk actions property quietly.
     *
     * @param  bool|(\Closure():bool)  $selectable
     */
    public static function setSelectable(bool|\Closure $selectable = true): void
    {
        static::$globalSelectable = $selectable;
    }

    /**
     * Determine if the row is selectable for bulk actions.
     */
    public function isSelectable(): bool
    {
        return (bool) ($this->evaluate($this->inspect('selectable', null)) ?? static::$globalSelectable);
    }

    /**
     * Determine if the row is not selectable for bulk actions.
     */
    public function isNotSelectable(): bool
    {
        return ! $this->isSelectable();
    }
}
