<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::resource('books', BookController::class);
Route::post('/books/deleted/{id}/restore', [BookController::class, 'restore'])->name('books.restore');
Route::delete('/books/deleted/{id}/purge', [BookController::class, 'purgeDeleted'])->name('books.purge');
Route::resource('borrowers', BorrowerController::class);
Route::resource('rentals', RentalController::class)->only(['index', 'create', 'store', 'show']);
Route::patch('/rentals/{rental}/return', [RentalController::class, 'returnBook'])->name('rentals.return');
