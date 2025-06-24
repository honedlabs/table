<?php

declare(strict_types=1);

use Honed\Table\Migrations\ViewMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends ViewMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('table');
            $table->string('scope');
            $table->text('view');
            $table->timestamps();

            $table->unique(['table', 'scope', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->getTableName());
    }
};
