<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Product extends Model
{
    protected $guarded = [];

    use Searchable;

    protected $casts = [
        'status' => Status::class,
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    /**
     * Dummy method to test the getter.
     *
     * @return string
     */
    public function price()
    {
        return '$10.00';
    }
}
