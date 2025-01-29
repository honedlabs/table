<?php

namespace Honed\Table\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    protected $guarded = [];

    use Searchable;

    protected $casts = [
        'status' => Status::class,
    ];

    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
