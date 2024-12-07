<?php

namespace Honed\Table\Http\DTOs;

use Illuminate\Http\Request;

final class InlineActionData
{
    /**
     * Create a transfer object for a bulk action.
     *
     * @param  string  $table
     */
    public function __construct(
        public readonly string|int $table,
        public readonly string $name,
        public readonly string|int $id,
    ) {}

    /**
     * Create the transfer object from the request.
     */
    public static function from(Request $request): static
    {
        return resolve(self::class, [
            'table' => $request->string('table'),
            'id' => $request->input('id'),
            'name' => $request->string('name'),
        ]);
    }
}
