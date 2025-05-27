<?php

namespace Workbench\App\Models;

use Honed\Table\Concerns\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    /**
     * @use \Honed\Table\HasTable<ProductTable>
     */
    use HasTable;

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected static $tableClass = ProductTable::class;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
