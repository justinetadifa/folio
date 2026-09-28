<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrower;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LibraryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function book(array $overrides = []): Book
    {
        return Book::create(array_merge(['title' => 'A Test Book', 'author' => 'Test Author', 'published_year' => 2020, 'price' => '250.50'], $overrides));
    }

    private function borrower(): Borrower
    {
        return Borrower::create(['name' => 'Test Reader', 'contact_number' => '09123456789']);
    }

    private function payload(Book $book, Borrower $borrower, ?string $date = null): array
    {
        return ['book_id' => $book->book_id, 'borrower_id' => $borrower->borrower_id, 'rental_date' => $date ?? today()->toDateString()];
    }

    public function test_book_crud_uses_the_prescribed_primary_key(): void
    {
        $data = ['title' => 'Clean Code', 'author' => 'Robert Martin', 'published_year' => 2008, 'price' => '350.75', 'description' => 'A test description'];
        $this->post(route('books.store'), $data)->assertSessionHasNoErrors()->assertRedirect();
        $book = Book::firstOrFail();
        $this->assertNotNull($book->book_id);
        $this->assertSame('Available', $book->status);
        $this->get(route('books.show', $book))->assertOk()->assertSee('Clean Code');
        $this->put(route('books.update', $book), array_merge($data, ['title' => 'Edited title']))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('Edited title', $book->fresh()->title);
        $this->delete(route('books.destroy', $book))->assertRedirect(route('books.index'));
        $this->assertDatabaseCount('books', 0);
    }

    public function test_borrower_crud_preserves_leading_zeros(): void
    {
        $this->post(route('borrowers.store'), ['name' => 'Ana Cruz', 'contact_number' => '09123456789'])->assertSessionHasNoErrors();
        $borrower = Borrower::firstOrFail();
        $this->assertSame('09123456789', $borrower->contact_number);
        $this->put(route('borrowers.update', $borrower), ['name' => 'Ana Reyes', 'contact_number' => '+63 912 345 6789'])->assertSessionHasNoErrors();
        $this->get(route('borrowers.show', $borrower))->assertOk()->assertSee('Ana Reyes');
        $this->delete(route('borrowers.destroy', $borrower))->assertRedirect(route('borrowers.index'));
        $this->assertDatabaseCount('borrowers', 0);
    }

    public static function invalidBookData(): array
    {
        return [['title', ''], ['author', ''], ['published_year', 0], ['published_year', 9999], ['price', -1], ['price', '4.567'], ['price', '1000000.00'], ['status', 'Rented']];
    }

    #[DataProvider('invalidBookData')]
    public function test_invalid_book_fields_are_rejected(string $field, mixed $value): void
    {
        $data = ['title' => 'Book', 'author' => 'Author', 'published_year' => 2000, 'price' => '100.00'];
        $data[$field] = $value;
        $this->post(route('books.store'), $data)->assertSessionHasErrors($field);
        $this->assertDatabaseCount('books', 0);
    }

    public function test_invalid_borrower_information_is_rejected(): void
    {
        $this->post(route('borrowers.store'), ['name' => '', 'contact_number' => 'not-a-number'])->assertSessionHasErrors(['name', 'contact_number']);
        $this->assertDatabaseCount('borrowers', 0);
    }

    public function test_renting_updates_both_records_and_relationships(): void
    {
        $book = $this->book();
        $borrower = $this->borrower();
        $this->post(route('rentals.store'), $this->payload($book, $borrower))->assertSessionHasNoErrors()->assertRedirect();
        $rental = Rental::firstOrFail();
        $this->assertSame('Rented', $book->fresh()->status);
        $this->assertSame('Rented', $rental->status);
        $this->assertNull($rental->return_date);
        $this->assertTrue($rental->book->is($book));
        $this->assertTrue($rental->borrower->is($borrower));
        $this->assertSame(1, $book->rentals()->count());
        $this->assertSame(1, $borrower->rentals()->count());
    }

    public function test_second_active_rental_is_rejected(): void
    {
        $book = $this->book();
        $borrower = $this->borrower();
        $data = $this->payload($book, $borrower);
        $this->post(route('rentals.store'), $data)->assertSessionHasNoErrors();
        $this->post(route('rentals.store'), $data)->assertSessionHasErrors('book_id');
        $this->assertDatabaseCount('rentals', 1);
        $this->assertSame('Rented', $book->fresh()->status);
    }

    public function test_return_changes_both_records_and_keeps_history(): void
    {
        $book = $this->book();
        $borrower = $this->borrower();
        $rental = app(RentalService::class)->rent($this->payload($book, $borrower));
        $this->patch(route('rentals.return', $rental), ['return_date' => today()->toDateString()])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('Available', $book->fresh()->status);
        $this->assertSame('Returned', $rental->fresh()->status);
        $this->assertSame(today()->toDateString(), $rental->fresh()->return_date->toDateString());
        $this->assertDatabaseCount('rentals', 1);
    }

    public function test_repeat_return_cannot_change_the_return_date(): void
    {
        $rental = app(RentalService::class)->rent($this->payload($this->book(), $this->borrower(), today()->subDays(3)->toDateString()));
        app(RentalService::class)->returnBook($rental, today()->subDay()->toDateString());
        $this->patch(route('rentals.return', $rental), ['return_date' => today()->toDateString()])->assertSessionHasErrors('return_date');
        $this->assertSame(today()->subDay()->toDateString(), $rental->fresh()->return_date->toDateString());
    }

    public function test_returned_book_can_be_rented_again_without_losing_history(): void
    {
        $book = $this->book();
        $borrower = $this->borrower();
        $old = app(RentalService::class)->rent($this->payload($book, $borrower));
        app(RentalService::class)->returnBook($old, today()->toDateString());
        $this->post(route('rentals.store'), $this->payload($book, $borrower))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('rentals', 2);
        $this->assertSame('Returned', $old->fresh()->status);
        $this->assertSame('Rented', $book->fresh()->status);
        // A stale return form for the earlier transaction must not release the new rental.
        $this->patch(route('rentals.return', $old), ['return_date' => today()->toDateString()])->assertSessionHasErrors('return_date');
        $this->assertSame('Rented', $book->fresh()->status);
        $this->assertSame(1, $book->rentals()->where('status', 'Rented')->count());
    }

    public function test_return_cannot_precede_rental_or_be_in_future(): void
    {
        $rental = app(RentalService::class)->rent($this->payload($this->book(), $this->borrower()));
        foreach ([today()->subDay(), today()->addDay()] as $day) {
            $this->patch(route('rentals.return', $rental), ['return_date' => $day->toDateString()])->assertSessionHasErrors('return_date');
        }
        $this->assertSame('Rented', $rental->fresh()->status);
        $this->assertSame('Rented', $rental->book->fresh()->status);
    }

    public function test_future_or_impossible_rental_dates_are_rejected(): void
    {
        $book = $this->book();
        $borrower = $this->borrower();
        foreach ([today()->addDay()->toDateString(), '2026-02-31'] as $date) {
            $this->post(route('rentals.store'), $this->payload($book, $borrower, $date))->assertSessionHasErrors('rental_date');
        }
        $this->assertDatabaseCount('rentals', 0);
    }

    public function test_rental_cannot_start_before_previous_return(): void
    {
        $book = $this->book();
        $borrower = $this->borrower();
        $rental = app(RentalService::class)->rent($this->payload($book, $borrower, today()->subDays(5)->toDateString()));
        app(RentalService::class)->returnBook($rental, today()->subDay()->toDateString());
        $this->post(route('rentals.store'), $this->payload($book, $borrower, today()->subDays(2)->toDateString()))->assertSessionHasErrors('rental_date');
        $this->assertDatabaseCount('rentals', 1);
        $this->assertSame('Available', $book->fresh()->status);
    }

    public function test_missing_foreign_records_are_rejected(): void
    {
        $this->post(route('rentals.store'), ['book_id' => 9999, 'borrower_id' => 9999, 'rental_date' => today()->toDateString()])->assertSessionHasErrors(['book_id', 'borrower_id']);
        $this->assertDatabaseCount('rentals', 0);
    }

    public function test_history_blocks_deleting_books_and_borrowers(): void
    {
        $book = $this->book();
        $borrower = $this->borrower();
        $rental = app(RentalService::class)->rent($this->payload($book, $borrower));
        foreach ([false, true] as $returned) {
            if ($returned) {
                app(RentalService::class)->returnBook($rental, today()->toDateString());
            }
            $this->delete(route('books.destroy', $book))->assertSessionHasErrors('delete');
            $this->delete(route('borrowers.destroy', $borrower))->assertSessionHasErrors('delete');
        }
        $this->assertDatabaseCount('books', 1);
        $this->assertDatabaseCount('borrowers', 1);
        $this->assertDatabaseCount('rentals', 1);
    }

    public function test_edit_cannot_override_availability(): void
    {
        $book = $this->book();
        $rental = app(RentalService::class)->rent($this->payload($book, $this->borrower()));
        $this->put(route('books.update', $book), ['title' => 'Changed title', 'author' => 'A', 'published_year' => 2000, 'price' => 100, 'status' => 'Available'])->assertSessionHasErrors('status');
        $this->assertSame('Rented', $book->fresh()->status);
    }

    public function test_failed_status_update_rolls_back_the_rental_insert(): void
    {
        $book = $this->book();
        $borrower = $this->borrower();
        Book::updating(fn () => throw new \RuntimeException('Simulated write failure'));
        try {
            app(RentalService::class)->rent($this->payload($book, $borrower));
            $this->fail('Expected a simulated failure.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Simulated write failure', $e->getMessage());
        } finally {
            Book::flushEventListeners();
        }
        $this->assertDatabaseCount('rentals', 0);
        $this->assertSame('Available', $book->fresh()->status);
    }

    public function test_failed_return_update_rolls_back_both_records(): void
    {
        $book = $this->book();
        $rental = app(RentalService::class)->rent($this->payload($book, $this->borrower()));
        Book::updating(fn () => throw new \RuntimeException('Simulated return failure'));
        try {
            app(RentalService::class)->returnBook($rental, today()->toDateString());
            $this->fail('Expected a simulated failure.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Simulated return failure', $e->getMessage());
        } finally {
            Book::flushEventListeners();
        }
        $this->assertSame('Rented', $rental->fresh()->status);
        $this->assertNull($rental->fresh()->return_date);
        $this->assertSame('Rented', $book->fresh()->status);
    }

    public function test_search_filters_pagination_and_escaped_output(): void
    {
        $this->book(['title' => '<script>alert(1)</script>', 'author' => 'Unique Author']);
        for ($i = 0; $i < 10; $i++) {
            $this->book(['title' => 'Other Book '.$i]);
        }
        $response = $this->get(route('books.index', ['q' => 'Unique']));
        $response->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('Other Book');
        $this->get(route('books.index', ['page' => 2]))->assertOk()->assertSee('Pagination');
        $rented = $this->book(['title' => 'Already out']);
        app(RentalService::class)->rent($this->payload($rented, $this->borrower()));
        $this->get(route('books.index', ['status' => 'Available']))->assertOk()->assertDontSee('Already out');
        $this->get(route('rentals.create'))->assertOk()->assertDontSee('Already out');
        $this->get(route('rentals.index', ['q' => 'Already out', 'status' => 'Rented']))->assertOk()->assertSee('Already out');
        $this->get(route('borrowers.index', ['q' => '09123']))->assertOk()->assertSee('Test Reader');
    }

    public function test_seed_is_consistent_repeat_safe_and_every_page_renders(): void
    {
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('books', 12);
        $this->assertDatabaseCount('borrowers', 6);
        $this->assertDatabaseCount('rentals', 8);
        $this->assertSame(4, Book::where('status', 'Rented')->count());
        $this->assertSame(4, Rental::where('status', 'Rented')->count());
        $this->assertSame(4, Rental::where('status', 'Returned')->whereNotNull('return_date')->count());
        $book = Book::firstOrFail();
        $borrower = Borrower::firstOrFail();
        $active = Rental::where('status', 'Rented')->firstOrFail();
        $returned = Rental::where('status', 'Returned')->firstOrFail();
        $urls = ['/', '/books', '/books/create', route('books.show', $book), route('books.edit', $book), '/borrowers', '/borrowers/create', route('borrowers.show', $borrower), route('borrowers.edit', $borrower), '/rentals', '/rentals/create', route('rentals.show', $active), route('rentals.show', $returned)];
        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_empty_pages_and_missing_record_responses(): void
    {
        foreach (['/', '/books', '/borrowers', '/rentals', '/rentals/create'] as $url) {
            $this->get($url)->assertOk();
        }
        foreach (['/books/99999', '/borrowers/99999', '/rentals/99999'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_invalid_filter_input_does_not_crash_the_query(): void
    {
        $this->get('/books?q[]=test')->assertSessionHasErrors('q');
        $this->get('/rentals?status=Invalid')->assertSessionHasErrors('status');
    }
}
