<?php

declare(strict_types=1);

namespace Honed\Table\Http\Controllers;

use Honed\Action\Http\Controllers\ActionController;
use Honed\Table\Table;

class TableController extends ActionController
{
    /**
     * {@inheritdoc}
     *
     * @return class-string<\Honed\Action\Contracts\Handles>
     */
    public function baseClass()
    {
        return Table::class;
    }
}
