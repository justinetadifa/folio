<?php

use App\Models\Book;
use App\Models\Borrower;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Run only against an isolated MySQL/MariaDB test database ending in _test. */
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (DB::getDriverName() !== 'mysql' || ! str_ends_with(DB::connection()->getDatabaseName(), '_test')) {
    fwrite(STDERR, "Use an isolated MySQL/MariaDB database ending in _test. See docs/TESTING.md.\n");
    exit(1);
}
if (($argv[1] ?? '') === 'worker') {
    [$mode,$bookId,$borrowerId,$rentalId,$ready] = array_slice($argv, 2);
    file_put_contents($ready, 'ready');
    try {
        if ($mode === 'rent') {
            app(RentalService::class)->rent(['book_id' => (int) $bookId, 'borrower_id' => (int) $borrowerId, 'rental_date' => today()->toDateString()]);
        } else {
            app(RentalService::class)->returnBook(Rental::findOrFail((int) $rentalId), today()->toDateString());
        }
        echo 'SUCCESS';
    } catch (ValidationException $e) {
        echo 'REJECTED';
    }
    exit(0);
}
$book = null;
$borrowers = [];
function race(string $mode, Book $book, array $borrowers, int $rentalId = 0): void
{
    $processes = [];
    $paths = [];
    DB::beginTransaction();
    Book::whereKey($book->book_id)->lockForUpdate()->firstOrFail();
    try {
        for ($i = 0; $i < 2; $i++) {
            $ready = tempnam(sys_get_temp_dir(), 'folio-race-');
            unlink($ready);
            $paths[] = $ready;
            $process = proc_open([PHP_BINARY, __FILE__, 'worker', $mode, (string) $book->book_id, (string) $borrowers[$i]->borrower_id, (string) $rentalId, $ready], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
            if (! is_resource($process)) {
                throw new RuntimeException('Could not start a concurrent worker.');
            }
            fclose($pipes[0]);
            $processes[] = [$process, $pipes];
        }
        $deadline = microtime(true) + 10;
        while ((! file_exists($paths[0]) || ! file_exists($paths[1])) && microtime(true) < $deadline) {
            usleep(20000);
        }
        if (! file_exists($paths[0]) || ! file_exists($paths[1])) {
            throw new RuntimeException('Worker startup timed out.');
        }
        // Both requests are launched while the parent holds the book lock.
        usleep(200000);
        DB::commit();
        $results = [];
        foreach ($processes as [$process,$pipes]) {
            stream_set_timeout($pipes[1], 15);
            $results[] = trim(stream_get_contents($pipes[1]));
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exit = proc_close($process);
            if ($exit !== 0) {
                throw new RuntimeException($error ?: 'Worker exited with an error.');
            }
        }
        sort($results);
        if ($results !== ['REJECTED', 'SUCCESS']) {
            throw new RuntimeException('Unexpected concurrent results: '.json_encode($results));
        }
        echo "PASS concurrent {$mode}: exactly one success and one rejection.\n";
    } finally {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        foreach ($processes as [$process,$pipes]) {
            if (is_resource($process)) {
                proc_terminate($process);
            }
        }
        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
try {
    $book = Book::create(['title' => 'Concurrency test '.bin2hex(random_bytes(4)), 'author' => 'Test fixture', 'published_year' => 2020, 'price' => 0]);
    foreach (['A', 'B'] as $suffix) {
        $borrowers[] = Borrower::create(['name' => 'Concurrent reader '.$suffix, 'contact_number' => '09000000000']);
    }
    race('rent', $book, $borrowers);
    if ($book->rentals()->count() !== 1 || $book->fresh()->status !== 'Rented') {
        throw new RuntimeException('Rent integrity failed.');
    }
    $rental = $book->rentals()->firstOrFail();
    race('return', $book, $borrowers, $rental->rental_id);
    if ($book->fresh()->status !== 'Available' || $rental->fresh()->status !== 'Returned') {
        throw new RuntimeException('Return integrity failed.');
    }
    echo "PASS final database state: one preserved, returned rental and one available book.\n";
} finally {
    if ($book) {
        Rental::where('book_id', $book->book_id)->delete();
        $book->delete();
    }
    foreach ($borrowers as $borrower) {
        $borrower->delete();
    }
}
