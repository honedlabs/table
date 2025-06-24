<?php

declare(strict_types=1);

namespace Honed\Table\Contracts;

interface CanListViews
{
    /**
     * Get the views stored for a given table or tables.
     *
     * @param  mixed|array<int, mixed>  $table
     * @return array<int, object>
     */
    public function stored($table);

    /**
     * Get the views stored for a given scope or scopes.
     *
     * @param  mixed|array<int, mixed>  $scope
     * @return array<int, object>
     */
    public function scoped($scope);
}
