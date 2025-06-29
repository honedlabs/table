<?php

declare(strict_types=1);

namespace Honed\Table\Drivers;

use Honed\Table\Contracts\Driver;
use Honed\Table\Facades\Views;
use Illuminate\Contracts\Events\Dispatcher;

class ArrayDriver implements Driver
{
    /**
     * The store's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The event dispatcher.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * The resolved views.
     *
     * @var array<string, array<string, array<string, mixed>>>
     */
    protected $resolved = [];

    /**
     * Create a new view resolver.
     */
    public function __construct(
        Dispatcher $events,
        string $name
    ) {
        $this->events = $events;
        $this->name = $name;
    }

    /**
     * Retrieve the view for the given table, name, and scope from storage.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  string  $scope
     * @return object|null
     */
    public function get($table, $name, $scope)
    {
        if (isset($this->resolved[$table][$scope][$name])) {
            $view = $this->resolved[$table][$scope][$name];

            return (object) [
                'name' => $name,
                'table' => $table,
                'scope' => $scope,
                'view' => $view,
            ];
        }

        return null;
    }

    /**
     * Retrieve the views for the given table and scopes from storage.
     *
     * @param  string  $table
     * @param  string|array<int, string>  $scopes
     * @return array<int, object>
     */
    public function list($table, $scopes)
    {
        $views = [];

        foreach ($this->resolved[$table] ?? [] as $scope => $scoped) {
            if (in_array($scope, (array) $scopes)) {
                foreach ($scoped as $name => $view) {
                    $views[] = (object) [
                        'name' => $name,
                        'view' => $view,
                    ];
                }
            }
        }

        return $views;
    }

    /**
     * Create a new view for the given table, name and scope.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  string  $scope
     * @param  array<string, mixed>  $view
     * @return void
     */
    public function create($table, $name, $scope, $view)
    {
        $this->set($table, $name, $scope, $view);
    }

    /**
     * Set the view for the given table and scope.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  string  $scope
     * @param  array<string, mixed>  $view
     * @return void
     */
    public function set($table, $name, $scope, $view)
    {
        $this->resolved[$table][$scope][$name] = $view;
    }

    /**
     * Delete the view for the given table and scope from storage.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  string  $scope
     * @return void
     */
    public function delete($table, $name, $scope)
    {
        unset($this->resolved[$table][$scope][$name]);
    }

    /**
     * Purge all views for the given table.
     *
     * @param  string|array<int, string>|null  $table
     * @return void
     */
    public function purge($table = null)
    {
        if ($table === null) {
            $this->resolved = [];
        } else {
            foreach ((array) $table as $t) {
                unset($this->resolved[$t]);
            }
        }
    }
}
