<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    public const RENTED = 'Rented';

    public const RETURNED = 'Returned';

    protected $primaryKey = 'rental_id';

    public $timestamps = false;

    protected $dateFormat = 'Y-m-d';

    protected $fillable = ['borrower_id', 'book_id', 'rental_date'];

    protected $attributes = ['status' => self::RENTED];

    protected function casts(): array
    {
        return ['rental_date' => 'date', 'return_date' => 'date'];
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class, 'borrower_id', 'borrower_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }
}
