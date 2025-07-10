<?php

declare(strict_types=1);

namespace Workbench\App\Tables;

use Honed\Table\Table;
use Workbench\App\Models\User;

/**
 * @template TModel of \Workbench\App\Models\User
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Table<TModel, TBuilder>
 */
class UserTable extends Table
{
    /**
     * Define the table.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this->for(User::class);
    }
}
