<?php

declare(strict_types=1);

namespace Honed\Table\Migrations;

use Honed\Table\Concerns\InteractsWithDatabase;
use Illuminate\Database\Migrations\Migration;

abstract class ViewMigration extends Migration
{
    use InteractsWithDatabase;
}
