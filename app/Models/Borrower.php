<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrower extends Model
{
    protected $primaryKey = 'borrower_id';

    public $timestamps = false;

    protected $fillable = ['name', 'contact_number'];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'borrower_id', 'borrower_id');
    }
}
