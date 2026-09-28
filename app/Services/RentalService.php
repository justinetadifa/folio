<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrower;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RentalService
{
    public function rent(array $data): Rental
    {
        return DB::transaction(function () use ($data) {
            // Lock the borrower before the book; deletion uses the same parent locks.
            $borrower = Borrower::whereKey($data['borrower_id'])->lockForUpdate()->first();
            if (! $borrower) {
                throw ValidationException::withMessages(['borrower_id' => 'This borrower no longer exists. Please choose another.']);
            }
            $book = Book::whereKey($data['book_id'])->lockForUpdate()->first();
            if (! $book) {
                throw ValidationException::withMessages(['book_id' => 'This book no longer exists. Please choose another.']);
            }
            if ($book->status !== Book::AVAILABLE || $book->rentals()->where('status', Rental::RENTED)->exists()) {
                throw ValidationException::withMessages(['book_id' => 'This book is already rented. Choose an available book.']);
            }
            $lastReturn = $book->rentals()->where('status', Rental::RETURNED)->max('return_date');
            $lastReturn = $lastReturn ? Carbon::parse($lastReturn)->toDateString() : null;
            if ($lastReturn && $data['rental_date'] < $lastReturn) {
                throw ValidationException::withMessages(['rental_date' => 'The rental date must be on or after the last return date ('.$lastReturn.').']);
            }
            $rental = Rental::create($data);
            $book->status = Book::RENTED;
            $book->save();

            return $rental;
        }, 5);
    }

    public function returnBook(Rental $rental, string $returnDate): Rental
    {
        return DB::transaction(function () use ($rental, $returnDate) {
            // Always lock the book before its rental; re-read both inside the transaction.
            $book = Book::whereKey($rental->book_id)->lockForUpdate()->firstOrFail();
            $current = Rental::whereKey($rental->rental_id)->lockForUpdate()->firstOrFail();
            if ($current->status !== Rental::RENTED) {
                throw ValidationException::withMessages(['return_date' => 'This rental has already been returned. No changes were made.']);
            }
            if ($returnDate < $current->rental_date->format('Y-m-d')) {
                throw ValidationException::withMessages(['return_date' => 'The return date cannot be before the rental date.']);
            }
            $current->status = Rental::RETURNED;
            $current->return_date = $returnDate;
            $current->save();
            $book->status = Book::AVAILABLE;
            $book->save();

            return $current;
        }, 5);
    }
}
