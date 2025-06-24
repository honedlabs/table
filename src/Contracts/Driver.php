<?php

declare(strict_types=1);

namespace Honed\Table\Contracts;

interface Driver
{
    /**
     * Retrieve the view for the given table, name, and scope from storage.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @return object|null
     */
    public function get($table, $name, $scope);

    /**
     * Retrieve the views for the given table and scopes from storage.
     *
     * @param  string  $table
     * @param  array<int, mixed>  $scopes
     * @return array<int, object>
     */
    public function list($table, $scopes);

    /**
     * Set the view for the given table and scope.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @param  array<string, mixed>  $value
     * @return void
     */
    public function set($table, $name, $scope, $value);

    /**
     * Delete the view for the given table and scope from storage.
     *
     * @param  string  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @return void
     */
    public function delete($table, $name, $scope);

    /**
     * Purge all views for the given table.
     *
     * @param  string|array<int, string>|null  $table
     * @return void
     */
    public function purge($table = null);
}
