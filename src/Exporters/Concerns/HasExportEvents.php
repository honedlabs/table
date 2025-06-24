<?php

declare(strict_types=1);

namespace Honed\Table\Exporters\Concerns;

trait HasExportEvents
{
    /**
     * The events that this export should listen to.
     *
     * @var array<class-string<\Maatwebsite\Excel\Events\Event>, callable>
     */
    protected $events = [];

    /**
     * Register the events that the export should listen to.
     *
     * @template T of \Maatwebsite\Excel\Events\Event
     *
     * @param  class-string<T>|array<class-string<T>, callable>  $events
     * @return $this
     */
    public function events($events)
    {
        /** @var array<class-string<T>, callable> */
        $events = is_array($events) ? $events : func_get_args();

        $this->events = [...$this->events, ...$events];

        return $this;
    }

    /**
     * Hook into the underlying event that the export should listen to.
     *
     * @param  class-string<\Maatwebsite\Excel\Events\Event>  $event
     * @param  callable  $callback
     * @return $this
     */
    public function event($event, $callback)
    {
        $this->events[$event] = $callback;

        return $this;
    }

    /**
     * Get the events that the export should listen to.
     *
     * @return array<class-string<\Maatwebsite\Excel\Events\Event>, callable>
     */
    public function getEvents()
    {
        return $this->events;
    }
}
