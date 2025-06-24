<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Honed\Table\Attributes\UseTable;
use Honed\Table\Concerns\HasTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Workbench\App\Tables\UserTable;
use Workbench\Database\Factories\UserFactory;

#[UseTable(UserTable::class)]
class User extends Authenticatable
{
    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Workbench\Database\Factories\UserFactory>
     */
    use HasFactory;

    /**
     * @use \Honed\Table\Concerns\HasTable<\Workbench\App\Tables\UserTable>
     */
    use HasTable;

    use Notifiable;

    /**
     * The factory for the model.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Factories\Factory>
     */
    protected static $factory = UserFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the products for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Product>
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the products that the user has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Product, $this>
     */
    public function purchasedProducts()
    {
        return $this->belongsToMany(Product::class);
    }
}
