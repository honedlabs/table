<?php

namespace Honed\Table\Http\DTOs;

use Illuminate\Http\Request;

final class InlineActionData
{
    /**
     * Create a transfer object for a bulk action.
     * 
     * @param string $table
     * @param string $name
     * @param string|int $id
     */
    public function __construct(
        public readonly string|int $table,
        public readonly string $name,
        public readonly string|int $id,
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
            'id' => $request->input('id'),
            'name' => $request->string('name'),
        ]);
    }
}