# Verification and Testing

The project was checked on 28 September 2026 (Asia/Manila).

## Executed checks

| Check | Result |
| --- | --- |
| Composer dependency installation | Successful; Laravel 12.69.2, PHP 8.3.6 |
| Frontend build | Successful; Tailwind 3.4.17 and local assets |
| Named application routes | 20 routes registered |
| Blade template compilation | Successful |
| SQLite functional suite | 28 tests passed, 164 assertions |
| MariaDB 10.11.14 functional suite, through the mysql driver | 28 tests passed, 164 assertions |
| Concurrent rent using two PHP worker processes and MariaDB | Exactly one success and one validation rejection |
| Concurrent return using two PHP worker processes and MariaDB | Exactly one success and one validation rejection |
| State after concurrent operations | One preserved returned rental; book Available |
| Real HTTP request without a CSRF token | Rejected with HTTP 419 |
| Real HTTP form with a valid session/token | Create, redirect, detail view, and method-spoofed delete succeeded |
| PHP formatting and JavaScript syntax | Passed |
| Clean ZIP extraction | Composer install, key generation, migrations, demo seeding, and all 28 tests succeeded |

The functional suite covers book/borrower CRUD, custom primary keys, relationships, invalid inputs, exact decimal limits, leading-zero contacts, rentals, returns, stale returns, date validation, deletion protection, rollback on failed writes, search, filters, pagination, escaped output, repeated seeding, empty pages, missing records, and rendering the main Blade pages.

The independent concurrency check starts two workers while a parent transaction holds the book row lock, then releases that lock. It verifies both the outcomes and final database state. SQLite test success alone is not used as evidence for row-lock behavior.

**Verification limits:** The provided browser blocked local-file previews under its security policy, so visual browser QA and browser-driven JavaScript interaction checks could not be completed here. Windows/XAMPP installation and Oracle MySQL itself were not run in this Linux workspace. The actual database checks used MariaDB with InnoDB. The responsive layouts and confirmation dialogs should be checked in your browser using the checklist below.

## Default functional suite

```bat
php artisan test
```

The included `phpunit.xml` uses SQLite `:memory:`. Tests run in an isolated database. Laravel's usual HTTP test harness bypasses CSRF middleware; a separate real HTTP check verified rejection of a tokenless POST and success of a valid-token form submission. Application forms include CSRF tokens and use the default web middleware.

## MySQL or MariaDB functional suite

Create an empty, disposable database named `folio_book_rental_test`. The test runner resets its schema. Do not use your demonstration database or another database with data you need.

Open a new **Command Prompt** in the project folder and set:

```bat
set DB_CONNECTION=mysql
set DB_HOST=127.0.0.1
set DB_PORT=3306
set DB_DATABASE=folio_book_rental_test
set DB_USERNAME=root
set DB_PASSWORD=
php artisan config:clear
php artisan test
```

Adjust credentials and port for your installation. Close this test terminal when finished so its environment overrides do not affect the normal application terminal. On PowerShell, use `$env:DB_CONNECTION = "mysql"` and equivalent assignments for the other variables.

## Separate concurrency check

Use the same disposable `_test` database and test terminal. It needs the migrations and PHP's `proc_open` function enabled:

```bat
php artisan migrate
php scripts/check-concurrency.php
```

Expected output:

```text
PASS concurrent rent: exactly one success and one rejection.
PASS concurrent return: exactly one success and one rejection.
PASS final database state: one preserved, returned rental and one available book.
```

The script refuses to run unless the driver is `mysql` and the database name ends in `_test`. It creates its own fixture book and two borrowers, then deletes those fixtures in cleanup. It does not need or alter the demo seed records.

## Browser and presentation checklist

- Open Overview, Books, Borrowers, and Rentals at desktop width and at about 390 px mobile width.
- Confirm text is readable, there is no page-wide horizontal overflow, and tables can scroll within their containers on small screens.
- Open and close the mobile navigation; test the Escape key and visible keyboard focus.
- Add a book and borrower, then complete a rent → return → rent-again sequence.
- Trigger a validation error. Confirm the error is visible and entered values remain available.
- Open the return confirmation and cancel it. Confirm no transaction changed. Repeat and confirm the return.
- Delete a record that has no rental history; cancel once before confirming. Use only a disposable demo record.
- Open a book with history and confirm deletion is unavailable; the server should also reject a direct delete attempt.
- Check search, clear filters, pagination, and status filters.
- With dependencies already installed, disconnect the internet temporarily and confirm styles, fonts, and controls still load locally.
- To independently verify CSRF protection, send a POST to `/books` without a session token using an HTTP client; the expected response is 419. Do not disable CSRF middleware.
- Run the classroom demo once after extracting and installing the final ZIP.

## Issues caught during development

A same-day re-rental initially failed on SQLite because a returned date could include a midnight timestamp. Rental dates now serialize as `Y-m-d`, and the historical comparison normalizes the date. The regression test passes on both tested engines.

The page-rendering test initially assumed seeded IDs always started at 1. MariaDB auto-increment counters survive transaction rollback, so the test now looks up the actual seeded models. The application itself uses model-based route generation and custom primary keys.
