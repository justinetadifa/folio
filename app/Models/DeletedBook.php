<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedBook extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'original_book_id',
        'title',
        'author',
        'description',
        'published_year',
        'price',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'published_year' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }
}
