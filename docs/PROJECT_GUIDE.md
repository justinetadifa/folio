# Project Guide

Folio fulfills the supplied Book Rental System assignment using Laravel's MVC architecture. The core deliverable is a working local application with three related business tables. The interface and dashboard are enhancements around that required workflow.

## Database schema

The migration files preserve the assignment's names and data types. Auto-incrementing primary keys and their foreign keys use matching unsigned bigint types.

| Table | Column | Database type | Purpose |
| --- | --- | --- | --- |
| borrowers | borrower_id | unsigned bigint, primary key | Borrower identity |
| borrowers | name | varchar(255) | Full name |
| borrowers | contact_number | varchar(255) | Contact text; preserves leading zeros |
| books | book_id | unsigned bigint, primary key | Book identity |
| books | title | varchar(255) | Book title |
| books | author | varchar(255) | Author |
| books | description | text, nullable | Optional description |
| books | published_year | integer | Publication year |
| books | price | decimal(8,2) | Book value |
| books | status | varchar(255) | Available or Rented |
| rentals | rental_id | unsigned bigint, primary key | Rental identity |
| rentals | borrower_id | unsigned bigint, foreign key | References borrowers.borrower_id |
| rentals | book_id | unsigned bigint, foreign key | References books.book_id |
| rentals | rental_date | date | Rental date |
| rentals | return_date | date, nullable | Actual return date |
| rentals | status | varchar(255) | Rented or Returned |

A borrower has many rentals. A book has many rentals over time. Each rental belongs to exactly one borrower and one book. Indexes support availability and rental queries. Foreign keys restrict deletion of referenced parents.

The models explicitly set `borrower_id`, `book_id`, and `rental_id` as their primary keys. They disable Eloquent timestamps because the assigned tables do not contain `created_at` or `updated_at`. Laravel also creates its own `migrations` tracking table; that is framework bookkeeping, not another business entity.

## How MVC works here

A browser request first matches a route in `routes/web.php`. A controller receives that request, validates or delegates validation, retrieves data through Eloquent models, and returns a Blade view or redirects after a change.

For example, submitting **Add book** sends a POST request to `books.store`. `BookRequest` validates the data, `BookController` creates a `Book`, and the browser is redirected to its details page. The model maps to the books table, while `books/show.blade.php` controls its presentation.

Renting spans two records, so `RentalController` delegates that operation to `RentalService`. This small service keeps the transaction rules together and makes them reusable by seeders and tests. Controllers still handle HTTP requests; the service does not render pages.

## Rental consistency

On rent, the service opens a database transaction, locks the borrower and book rows, and rechecks that the book is Available and has no active rental. It inserts a Rented rental with an empty return date, updates the book to Rented, then commits both changes together.

On return, the service locks the book and then re-reads and locks the rental. It rejects a rental that has already been returned, validates the return date, marks the rental Returned, records the return date, and marks the book Available in one transaction.

The re-read matters: an old return page must never release a book that has since been rented through a new transaction. If either database write fails, the transaction rolls back. If two requests compete for the same book, the later request rechecks the state after the lock becomes available.

Deletion also locks its parent record and checks for rental history. A borrower or book with any rental history is retained. This is an intentional implementation choice to preserve the required history; records with no rental history can be deleted normally.

## Implementation choices

- Each book row represents one physical copy. Copies can share a title; no ISBN, stock quantity, or inventory-copy table was added.
- Prices use decimal(8,2), following the sample migration, and display in Philippine pesos. Price does not generate fees or revenue.
- Valid publication years are 1 through the current year. Prices may be zero, may have at most two decimal places, and must fit the database precision.
- Names and titles are required and limited to 255 characters. Contact input accepts 7–30 characters with digits, spaces, parentheses, hyphens, and an optional leading plus.
- Rental and return dates cannot be in the future. A return cannot precede its rental. A new rental cannot be backdated before the book's most recent return. A return and a new rental on the same day are allowed.
- The application timezone is Asia/Manila, configurable through `APP_TIMEZONE`.
- Status fields are maintained by the application. The book form does not let users override availability.
- Rental history is viewed and returned through dedicated actions; arbitrary editing or deleting of rental records is not offered.
- Authentication was not required by the assignment. The delivered workflow is intended for a trusted local classroom demonstration.

## Requirement mapping

| Assignment requirement | Implementation |
| --- | --- |
| Laravel and MVC | Laravel 12, routes, Eloquent models, controllers, Blade views |
| Manage books | BookController, BookRequest, books views |
| Manage borrowers | BorrowerController, BorrowerRequest, borrowers views |
| Exact database structure | Three migration files and custom model primary keys |
| Borrower has many rentals | Borrower::rentals() |
| Book has many rentals | Book::rentals() |
| Rental belongs to borrower and book | Rental::borrower(), Rental::book() |
| Only Available books can be rented | RentalService::rent(), available-book selector |
| Rent changes book to Rented | Atomic rental creation and book update |
| Return changes book to Available | RentalService::returnBook() |
| Return records date and Returned status | Validated PATCH rental return action |

Dashboard counts, search, pagination, filters, input validation, row locking, deletion safeguards, confirmation dialogs, and the custom design are enhancements. No unsupported due-date, fine, payment, or revenue rules were invented.

## Corrections to the sample handout

1. The sample's controller-creation step repeats `php artisan make:model Book`. A controller is generated with `php artisan make:controller BookController --resource`.
2. Use an explicit migration name such as `php artisan make:migration create_books_table`, or generate it with `php artisan make:model Book -m`. The handout's `make:migration create books` is not the intended migration-generator form.
3. The sample uses a generic `id`; this assignment requires `book_id`, `borrower_id`, and `rental_id`.
4. The sample's `latest()` query assumes `created_at`. This project orders explicitly by its primary keys or names because the assigned schema has no timestamps.
5. The sample covers books alone. The completed activity adds borrowers, relationships, and rental/return transactions.
6. The sample loads Tailwind from a CDN. Folio includes compiled CSS and local fonts so the classroom UI works without that dependency after installation.

These notes explain how the sample was adapted. The project already contains the generated files; do not run the generators over the existing implementation.

## Five-minute classroom demonstration

1. Open **Overview** and point out the counts: 12 books, 8 available, 4 rented, and 6 borrowers immediately after fresh demo seeding.
2. Open **Books**. Search by author, filter Available books, and open a book to show its details and history.
3. Add a new book with a title, author, year, and price. Show that its initial status is Available.
4. Add a borrower. Include a leading zero in the contact number and show that it is preserved.
5. Choose **New rental**, select the new book and borrower, and use today's date. Show the Rented status and the transaction reference.
6. Open New rental again and show that this book no longer appears in the available list. Server validation also rejects attempts to rent it again.
7. Open the active rental, select **Record return**, and confirm. Show Returned, the return date, and the book's Available status.
8. Rent the same book again. Show two separate records in its history. The earlier return remains intact.
9. Edit a book or borrower. Explain that records with history remain in the system. Demonstrate deletion using a separate, unused record.
10. Run `php artisan test`. Briefly explain custom primary keys, relationships, validation, and the transaction in `RentalService`.

## Short explanation for your professor

“Folio follows Laravel's Model–View–Controller pattern. The models represent books, borrowers, and rentals and define their relationships. Controllers handle requests, while Blade views present the interface. The rental service uses a database transaction so creating a rental and changing book availability succeed or fail together. Row locks and server-side checks prevent double rentals. Returns preserve the original rental record, so each book and borrower has a complete history.”

Review the code alongside this explanation and practice the flow before presenting. Add your own student and course information wherever your professor requires it; none has been guessed.
