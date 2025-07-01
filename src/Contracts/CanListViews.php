<?php

declare(strict_types=1);

namespace Honed\Table\Contracts;

interface CanListViews
{
    /**
     * Get all the views stored for all tables.
     *
     * @return array<int, object>
     */
    public function all(): array;

    /**
     * Get the views stored for a given table or tables.
     *
     * @param  mixed|array<int, mixed>  $table
     * @return array<int, object>
     */
    public function stored(mixed $table): array;

    /**
     * Get the views stored for a given scope or scopes.
     *
     * @param  mixed|array<int, mixed>  $scopes
     * @return array<int, object>
     */
    public function scoped(mixed $scopes): array;
}
