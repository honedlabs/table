<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Interpret;
use Honed\Core\Pipe;
use Honed\Table\Columns\Column;

/**
 * @template TClass of \Honed\Table\Table
 *
 * @extends Pipe<TClass>
 */
class Toggle extends Pipe
{
    /**
     * Run the toggle logic.
     */
    public function run(): void
    {
        $instance = $this->instance;

        $toggleable = $instance->isToggleable();
        $orderable = $instance->isOrderable();

        if (! $toggleable && ! $orderable) {
            return;
        }

        $names = $this->getColumns($instance);

        if ($orderable) {
            $this->order($instance, $names);
        }

        if ($toggleable) {
            $this->toggle($instance, $names);
        }

        $this->persist($instance, $names);
    }

    /**
     * Get the columns which should be displayed.
     *
     * @param  TClass  $instance
     * @return array<int, string>|null
     */
    protected function getColumns($instance)
    {
        $request = $instance->getRequest();

        $key = $instance->getColumnKey();

        $params = Interpret::array(
            $request, $key, $instance->getDelimiter(), 'string'
        );

        return match (true) {
            (bool) $params => $params,
            $request->missing($key) => $this->persisted($instance),
            default => null,
        };
    }

    /**
     * Toggle and order the columns, setting the active status.
     *
     * @param  TClass  $instance
     * @param  array<int, string>|null  $names
     * @return void
     */
    protected function toggle($instance, $names)
    {
        $columns = $instance->getColumns();

        $headings = array_values(
            array_filter(
                $columns,
                function (Column $column) use ($names) {
                    $active = $this->isActive($column, $names);

                    $column->active($active);

                    if (! $active
                        && ($column->isAlways() || $column->isKey())
                    ) {
                        return true;
                    }

                    return $active;
                }
            )
        );

        $instance->setHeadings($headings);
    }

    /**
     * Order columns based on the provided order.
     *
     * @param  TClass  $instance
     * @param  array<int, string>|null  $names
     * @return void
     */
    protected function order($instance, $names)
    {
        if (! $names) {
            return;
        }

        $columns = $instance->getColumns();

        /** @var array<string, Column> */
        $map = [];

        foreach ($columns as $column) {
            $map[$column->getParameter()] = $column;
        }

        /** @var array<int, Column> */
        $orderedColumns = [];

        foreach ($names as $name) {
            if (isset($map[$name])) {
                $orderedColumns[] = $map[$name];
                unset($map[$name]);
            }
        }

        foreach ($columns as $column) {
            if (isset($map[$column->getParameter()])) {
                $orderedColumns[] = $column;
            }
        }

        $instance->setColumns($orderedColumns);
    }

    /**
     * Activate and update the columns based on the toggle state.
     *
     * @param  Column  $column
     * @param  array<int, string>|null  $names
     * @return bool
     */
    protected function isActive($column, $names)
    {
        return match (true) {
            ! $column->isToggleable() => true,
            ! $names => $column->isToggledByDefault(),
            default => in_array($column->getParameter(), $names),
        };
    }

    /**
     * Get the persisted columns from the store.
     *
     * @param  TClass  $instance
     * @return array<int, string>|null
     */
    protected function persisted($instance)
    {
        $data = $instance->getColumnStore()?->get($instance->getColumnKey());

        if (! is_array($data)) {
            return null;
        }

        /** @var array<int, string> $data */
        return $data;
    }

    /**
     * Persist the columns which should be displayed.
     *
     * @param  TClass  $instance
     * @param  array<int, string>|null  $params
     * @return void
     */
    protected function persist($instance, $params)
    {
        $instance->getColumnStore()?->put($instance->getColumnKey(), $params);
    }
}
