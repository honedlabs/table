<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Pagination\Concerns\Classes;

use Honed\Table\Pagination\Enums\Paginator;
use Honed\Table\Table;

final class PropertyTable extends Table
{
    protected $showKey = 'count';

    protected $perPage = 20;

    protected $pageName = 'p';

    protected $defaultPerPage = 20;

    protected $paginator = Paginator::Cursor;
}
