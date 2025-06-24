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
     * @param  mixed  $scope
     * @return object|null
     */
    public function get($table, $name, $scope)
    {
        $scopeKey = Views::serializeScope($scope);

        if (isset($this->resolved[$table][$scopeKey][$name])) {
            $view = $this->resolved[$table][$scopeKey][$name];

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
     * @param  mixed|array<int, mixed>  $scopes
     * @return array<int, object>
     */
    public function list($table, $scopes)
    {
        $scopes = array_map(
            static fn ($scope) => Views::serializeScope($scope),
            (array) $scopes
        );

        $views = [];

        foreach ($this->resolved[$table] ?? [] as $scopeKeys => $scoped) {
            if (in_array($scopeKeys, $scopes)) {
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
     * Set the view for the given table and scope.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @param  array<string, mixed>  $value
     * @return void
     */
    public function set($table, $name, $scope, $value)
    {
        $scopeKey = Views::serializeScope($scope);

        $this->resolved[$table][$scopeKey][$name] = $value;
    }

    /**
     * Delete the view for the given table and scope from storage.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @return void
     */
    public function delete($table, $name, $scope)
    {
        $scopeKey = Views::serializeScope($scope);

        unset($this->resolved[$table][$scopeKey][$name]);
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
            /** @var array<int, string> */
            $tables = is_array($table) ? $table : func_get_args();

            foreach ($tables as $table) {
                unset($this->resolved[$table]);
            }
        }
    }
}
