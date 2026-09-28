<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    public const AVAILABLE = 'Available';

    public const RENTED = 'Rented';

    protected $primaryKey = 'book_id';

    public $timestamps = false;

    protected $fillable = ['title', 'author', 'description', 'published_year', 'price'];

    protected $attributes = ['status' => self::AVAILABLE];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'published_year' => 'integer'];
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'book_id', 'book_id');
    }
}
