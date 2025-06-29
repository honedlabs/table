<?php

declare(strict_types=1);

namespace Honed\Table\Contracts;

interface Driver
{
    /**
     * Retrieve the view for the given table, name, and scope from storage.
     *
     * @param  mixed  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @return object|null
     */
    public function get($table, $name, $scope);

    /**
     * Retrieve the views for the given table and scopes from storage.
     *
     * @param  mixed  $table
     * @param  array<int, mixed>  $scopes
     * @return array<int, object>
     */
    public function list($table, $scopes);

    /**
     * Create a new view for the given table and scope.
     *
     * @param  mixed  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @param  array<string, mixed>  $view
     * @return void
     */
    public function create($table, $name, $scope, $view);

    /**
     * Set the view for the given table and scope.
     *
     * @param  mixed  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @param  array<string, mixed>  $view
     * @return void
     */
    public function set($table, $name, $scope, $view);

    /**
     * Delete the view for the given table and scope from storage.
     *
     * @param  mixed  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @return void
     */
    public function delete($table, $name, $scope);

    /**
     * Purge all views for the given table.
     *
     * @param  mixed|array<int, mixed>|null  $table
     * @return void
     */
    public function purge($table = null);
}
