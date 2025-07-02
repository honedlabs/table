<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Honed\Table\Concerns\HasTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Workbench\App\Enums\Status;
use Workbench\App\Tables\ProductTable;
use Workbench\Database\Factories\ProductFactory;

class Product extends Model
{
    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Database\Factories\ProductFactory>
     */
    use HasFactory;

    use HasTable;
    use Searchable;
    use SoftDeletes;

    /**
     * The factory for the model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Factories\Factory>
     */
    protected static $factory = ProductFactory::class;

    /**
     * The table for the model.
     *
     * @var class-string<\Honed\Table\Table>
     */
    protected static $tableClass = ProductTable::class;

    /**
     * The attributes that cannot be mass assigned.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string>
     */
    protected $casts = [
        'status' => Status::class,
    ];

    /**
     * Get the user that owns the product.
     *
     * @return BelongsTo<User, $this>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the users that have the product.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the search data for the model using Laravel Scout.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
}
