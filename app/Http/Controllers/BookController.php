<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in([Book::AVAILABLE, Book::RENTED])]]);
        $books = Book::query()
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where(fn ($q) => $q->where('title', 'like', "%{$term}%")->orWhere('author', 'like', "%{$term}%")))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('book_id')->paginate(8)->withQueryString();

        $deletedBooks = \App\Models\DeletedBook::orderByDesc('deleted_at')->get();

        return view('books.index', compact('books', 'deletedBooks'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(BookRequest $request)
    {
        $book = Book::create($request->validated());

        return redirect()->route('books.show', $book)->with('success', 'Book added to the collection.');
    }

    public function show(Book $book)
    {
        $rentals = $book->rentals()->with('borrower')->orderByDesc('rental_id')->paginate(8);

        return view('books.show', compact('book', 'rentals'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(BookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return redirect()->route('books.show', $book)->with('success', 'Book details updated.');
    }

    public function destroy(Book $book)
    {
        $deletedTitle = $book->title;
        $archiveId = null;

        DB::transaction(function () use ($book, &$archiveId) {
            $current = Book::whereKey($book->book_id)->lockForUpdate()->firstOrFail();
            if ($current->rentals()->exists()) {
                throw ValidationException::withMessages(['delete' => 'This book has rental history and cannot be deleted. Its transaction records are preserved.']);
            }

            // Save to DeletedBook archive before deleting from books
            $archive = \App\Models\DeletedBook::create([
                'original_book_id' => $current->book_id,
                'title' => $current->title,
                'author' => $current->author,
                'description' => $current->description,
                'published_year' => $current->published_year,
                'price' => $current->price,
                'deleted_at' => now(),
            ]);
            $archiveId = $archive->id;

            $current->delete();
        }, 5);

        return redirect()->route('books.index')
            ->with('success', "Volume '{$deletedTitle}' moved to recently deleted.")
            ->with('restorable_id', $archiveId)
            ->with('restorable_title', $deletedTitle);
    }

    public function restore(int $id)
    {
        $archive = \App\Models\DeletedBook::findOrFail($id);

        $book = DB::transaction(function () use ($archive) {
            $restored = Book::create([
                'title' => $archive->title,
                'author' => $archive->author,
                'description' => $archive->description,
                'published_year' => $archive->published_year,
                'price' => $archive->price,
                'status' => Book::AVAILABLE,
            ]);

            $archive->delete();

            return $restored;
        });

        return redirect()->route('books.show', $book)
            ->with('success', "Volume '{$book->title}' has been successfully restored to the collection.");
    }

    public function purgeDeleted(int $id)
    {
        $archive = \App\Models\DeletedBook::findOrFail($id);
        $title = $archive->title;
        $archive->delete();

        return redirect()->route('books.index')
            ->with('success', "Volume '{$title}' permanently purged from archive.");
    }
}
