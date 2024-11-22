<?php

namespace Honed\Table\Http\DTOs;

use Illuminate\Http\Request;

final class BulkActionData
{
    /**
     * Create a transfer object for a bulk action.
     * 
     * @param string $id
     * @param string $name
     * @param bool $all
     * @param array<array-key, string|int> $except
     * @param array<array-key, string|int> $only
     */
    public function __construct(
        public readonly string $table,
        public readonly string $name,
        public readonly bool $all,
        public readonly array $except,
        public readonly array $only,
    ) {}

    /**
     * Create the transfer object from the request.
     * 
     * @param \Illuminate\Http\Request $request
     * @return static
     */
    public static function from(Request $request): static
    {
        return resolve(static::class, [
            'table' => $request->string('table'),
            'name' => $request->string('name'),
            'only' => $request->input('only', []),
            'all' => $request->boolean('all'),
            'except' => $request->input('except', []),
        ]);
    }
}