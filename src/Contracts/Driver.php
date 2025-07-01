<?php

declare(strict_types=1);

namespace Honed\Table\Contracts;

interface Driver
{
    /**
     * Retrieve the view for the given table, name, and scope from storage.
     *
     * @return object|null
     */
    public function get(mixed $table, string $name, mixed $scope);

    /**
     * Retrieve the views for the given table and scopes from storage.
     *
     * @param  mixed|array<int, mixed>  $scopes
     * @return array<int, object>
     */
    public function list(mixed $table, mixed $scopes): array;

    /**
     * Create a new view for the given table and scope.
     *
     * @param  array<string, mixed>  $view
     */
    public function create(mixed $table, string $name, mixed $scope, array $view): void;

    /**
     * Set the view for the given table and scope.
     *
     * @param  array<string, mixed>  $view
     */
    public function set(mixed $table, string $name, mixed $scope, array $view): void;

    /**
     * Delete the view for the given table and scope from storage.
     */
    public function delete(mixed $table, string $name, mixed $scope): void;

    /**
     * Purge all views for the given table.
     *
     * @param  mixed|array<int, mixed>|null  $table
     */
    public function purge(mixed $table = null): void;
}
