<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait Selectable
{
    /**
     * Whether the records are selectable for bulk actions.
     *
     * @var (\Closure(mixed...):bool)|null
     */
    protected $selectable;

    /**
     * Set the closure for whether a row is selectable quietly quietly.
     *
     * @param  (\Closure(mixed...):bool)|null  $selectable
     */
    public function setSelectable(?\Closure $selectable): void
    {
        $this->selectable = $selectable;
    }

    /**
     * Retrieve the selectable closure.
     *
     * @return (\Closure(mixed...):bool)|array{0:$this,1:string}|null
     */
    public function getSelector(): \Closure|array|null
    {
        return match (true) {
            \property_exists($this, 'selectable') && ! \is_null($this->selectable) => $this->selectable,
            \method_exists($this, 'selectable') => [$this, 'selectable'],
            default => null,
        };
    }

    /**
     * Determine if the table has a selectable closure available.
     */
    public function hasSelector(): bool
    {
        return ! \is_null($this->getSelector());
    }

    /**
     * Determine if a record is selectable using the provided closure.
     */
    public function isSelectable(mixed $record): bool
    {
        return ! $this->hasSelector()
            ? true
            : \call_user_func($this->getSelector(), $record);
    }
}
