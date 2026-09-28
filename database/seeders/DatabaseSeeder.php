<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Borrower;
use App\Services\RentalService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Never overwrite a working collection when db:seed is run again.
        if (Book::exists() || Borrower::exists()) {
            $this->command?->warn('Demo seed skipped: the database already contains books or borrowers.');

            return;
        }
        DB::transaction(function () {
            $titles = [
                ['Noli Me Tangere', 'José Rizal', 1887, 295, 'A landmark Philippine novel exploring identity, justice, and society under colonial rule.'],
                ['El Filibusterismo', 'José Rizal', 1891, 325, 'The companion to Noli Me Tangere, following an ambitious plan for change and its consequences.'],
                ['The Little Prince', 'Antoine de Saint-Exupéry', 1943, 260, 'A traveler from a small planet offers a thoughtful look at friendship, responsibility, and what matters most.'],
                ['Pride and Prejudice', 'Jane Austen', 1813, 380, 'Elizabeth Bennet navigates family expectations, first impressions, and an unexpected relationship.'],
                ['The Great Gatsby', 'F. Scott Fitzgerald', 1925, 310, 'A portrait of ambition and longing set against the glittering social world of the Jazz Age.'],
                ['A Room of One’s Own', 'Virginia Woolf', 1929, 285, 'An extended essay considering the conditions that allow writers to create and be heard.'],
                ['The Secret Garden', 'Frances Hodgson Burnett', 1911, 290, 'An isolated child discovers a neglected garden and the possibility of a new beginning.'],
                ['Little Women', 'Louisa May Alcott', 1868, 420, 'Four sisters grow into their own ambitions while keeping family and friendship close.'],
                ['The Art of War', 'Sun Tzu', 2005, 225, 'A modern edition of the classic work on strategy, preparation, and understanding conflict.'],
                ['A Study in Scarlet', 'Arthur Conan Doyle', 1887, 275, 'The first meeting of Sherlock Holmes and Dr. Watson begins an enduring detective partnership.'],
                ['The Adventures of Tom Sawyer', 'Mark Twain', 1876, 340, 'A spirited childhood adventure along the Mississippi River.'],
                ['The Time Machine', 'H. G. Wells', 1895, 250, 'An inventor travels into the distant future and encounters an unsettling vision of humanity.'],
            ];
            $books = [];
            foreach ($titles as [$title, $author, $year, $price, $description]) {
                $books[] = Book::create(['title' => $title, 'author' => $author, 'published_year' => $year, 'price' => $price, 'description' => $description]);
            }
            $borrowers = [];
            foreach (['Avery Santos', 'Jamie Rivera', 'Sam de la Cruz', 'Alex Mendoza', 'Casey Reyes', 'Morgan Flores'] as $i => $name) {
                // Deliberately fictional demonstration contact numbers.
                $borrowers[] = Borrower::create(['name' => $name, 'contact_number' => '0900000000'.($i + 1)]);
            }
            $service = app(RentalService::class);
            foreach ([[0, 0, 16, 12], [2, 1, 12, 9], [4, 2, 8, 5], [7, 3, 6, 3]] as [$b,$u,$start,$end]) {
                $rental = $service->rent(['book_id' => $books[$b]->book_id, 'borrower_id' => $borrowers[$u]->borrower_id, 'rental_date' => today()->subDays($start)->toDateString()]);
                $service->returnBook($rental, today()->subDays($end)->toDateString());
            }
            foreach ([[0, 4, 2], [3, 0, 3], [6, 5, 1], [10, 2, 0]] as [$b,$u,$days]) {
                $service->rent(['book_id' => $books[$b]->book_id, 'borrower_id' => $borrowers[$u]->borrower_id, 'rental_date' => today()->subDays($days)->toDateString()]);
            }
        });
    }
}
