<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
