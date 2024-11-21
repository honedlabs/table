<?php

declare(strict_types=1);

namespace Honed\Table\Pipes\Contracts;

use Closure;
use Honed\Table\Table;

interface PipelinesTable
{
    public function handle(Table $table, Closure $next);
}
