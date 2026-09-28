<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'borrower_id' => ['required', 'integer', 'exists:borrowers,borrower_id'],
            'book_id' => ['required', 'integer', 'exists:books,book_id'],
            'rental_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'return_date' => ['prohibited'],
            'status' => ['prohibited'],
        ];
    }

    public function attributes(): array
    {
        return ['book_id' => 'book', 'borrower_id' => 'borrower'];
    }
}
