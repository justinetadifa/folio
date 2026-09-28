# Folio Book Rental System

A complete Laravel MVC application for the Book Rental System activity. Folio manages books and borrowers, records rentals and returns, and keeps a permanent transaction history. It follows the three-table schema in the supplied assignment.

**Start here:** Extract this folder, then follow the Windows instructions below. `START_HERE.html` provides a short guide you can open directly in your browser.

## What is included

- Dashboard with live counts and recent transactions.
- Books and borrowers: create, view, edit, search, and delete records that have no rental history.
- Rentals: choose an available book, record its borrower and rental date, then record its return.
- Rental history, availability/status filters, and pagination.
- Server validation, database transactions, row locks, foreign keys, and protection against repeated returns.
- Responsive Blade/Tailwind interface, local fonts, and confirmation dialogs.
- 12 demo books, 6 fictional borrowers, and 8 rental transactions: 4 active and 4 returned.
- Automated tests, a separate MySQL/MariaDB concurrency check, and a classroom demonstration guide.

## Requirements

| Component | Requirement or included version |
| --- | --- |
| PHP | 8.2 or newer within the Laravel 12 supported range; tested with 8.3.6 |
| Laravel | 12.69.2, pinned in `composer.lock` |
| Composer | 2.x |
| Database | MySQL 8+ or MariaDB 10.4+ using InnoDB; tested with MariaDB 10.11.14 |
| Frontend | Blade and Tailwind CSS 3.4.17 |
| Node.js | 22+ when rebuilding CSS; tested with 24.19.0 |
| Browser | A current desktop or mobile browser |

PHP needs Laravel's usual extensions, including `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `fileinfo`, `ctype`, `tokenizer`, and XML/DOM support. The default automated tests additionally need `pdo_sqlite`. `composer check-platform-reqs` checks the installed PHP environment against the locked dependencies.

The CSS, JavaScript, fonts, and favicon are already built in `public`. **Node.js and an internet connection are not required to display the interface after Composer dependencies are installed.** The first Composer installation requires internet access.

Laravel 12 was selected to retain PHP 8.2 compatibility. The dependency resolver is configured for PHP 8.2.0 so installing on a newer development machine does not silently create an 8.3-only dependency set. The installed framework version is recorded in the lockfile.

## Windows setup with XAMPP

### 1. Extract and open the project

Extract the ZIP so the project folder is, for example:

```text
C:\xampp\htdocs\folio-book-rental
```

Open that folder in VS Code. Open **Command Prompt** in the project folder:

```bat
cd C:\xampp\htdocs\folio-book-rental
php -v
composer --version
```

If `php` is not recognized, add your PHP folder, usually `C:\xampp\php`, to Windows PATH and open a new terminal. Use `where php` and `php --ini` to confirm the terminal uses the intended PHP installation. PHP versions below 8.2 must be upgraded before installation.

### 2. Install PHP dependencies and configure the app

```bat
composer install
copy .env.example .env
php artisan key:generate
composer check-platform-reqs
```

Run the `copy` command only for a fresh installation; copying again replaces your local settings. The application key is generated on your computer. A real `.env` and an application key are deliberately excluded from this archive.

### 3. Create the database

Start **MySQL** in the XAMPP Control Panel. If using phpMyAdmin to create the database, start Apache too. In phpMyAdmin's SQL tab, run:

```sql
CREATE DATABASE folio_book_rental
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Open `.env` and set your local credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=folio_book_rental
DB_USERNAME=root
DB_PASSWORD=
```

Use your configured port if it differs, such as `3307`. Set `DB_PASSWORD` if your database account has a password. XAMPP's database server may identify itself as MariaDB; use Laravel's `mysql` connection for this project.

### 4. Create the tables and demo data

```bat
php artisan optimize:clear
php artisan migrate --seed
```

The seeder creates data only when the books and borrowers tables are empty. Running it again preserves existing records and prints a skip notice. To start with an empty collection instead, use `php artisan migrate` without `--seed`.

### 5. Open Folio

```bat
php artisan serve
```

Open **http://127.0.0.1:8000**. Keep the terminal running while using the app. Use the URL printed by Artisan if it selects another port. Apache is not needed for `artisan serve`; the database server must stay running.

The app is a local library-desk activity and does not require a login. Keep it local; authentication and deployment configuration would be separate work before any public use.

### 6. Rebuild the frontend only when changing its source

The included assets already work. To make design changes:

```bat
npm ci
npm run build
```

Edit `resources/css/app.css`, `resources/js/app.js`, and the Blade files, then rebuild. `npm run build` compiles Tailwind, copies JavaScript, and copies the locally hosted font files and licenses. `npm run dev` watches CSS while editing; run the full build after JavaScript changes.

## Run the tests

```bat
php artisan test
```

The default suite uses an in-memory SQLite database isolated from the application's MySQL database. Enable `pdo_sqlite` in the **CLI** PHP configuration if needed. The suite does not need an existing SQLite database file.

See [docs/TESTING.md](docs/TESTING.md) for the MySQL/MariaDB test configuration, the concurrency check, actual verification results, and remaining manual checks. Never point a test suite at a database containing records you need to retain.

## Normal startup after installation

1. Start the database server in XAMPP.
2. Open a terminal in `folio-book-rental`.
3. Run `php artisan serve`.
4. Visit the displayed local URL.

You do not need to run Composer, seed the database, or rebuild assets every time.

## Using the application

1. **Books → Add book:** enter title, author, publication year, book price, and optional description. A new book is Available.
2. **Borrowers → Add borrower:** enter the reader's name and contact number. Leading zeros are preserved.
3. **New rental:** select the borrower and an Available book, then record the rental date. Book and rental become Rented together.
4. **Rentals → View / return:** choose the return date, select Record return, and confirm. The rental becomes Returned; the book becomes Available.
5. **Book or borrower details:** review the preserved rental history. A returned book can be rented again through a new transaction.

The book price is its recorded value in Philippine pesos, not a rental fee. There is no due-date, overdue, payment, or fine calculation because the assignment does not define those fields or rules.

## Project map

```text
app/Models/                     Book, Borrower, Rental and their relationships
app/Http/Controllers/           Request handling and page data
app/Http/Requests/              Server-side validation
app/Services/RentalService.php  Atomic rent and return operations
routes/web.php                  Named page and transaction routes
database/migrations/            The three prescribed tables
database/seeders/               Consistent demo data
resources/views/                Blade layouts, pages, and components
resources/css/                  Tailwind source and design styles
resources/js/                   Navigation and confirmation dialogs
public/                         Entry point and built local assets
scripts/check-concurrency.php   Real two-process database check
tests/Feature/                  Functional tests
docs/PROJECT_GUIDE.md           Schema, MVC, requirement mapping, and demo script
docs/TESTING.md                 Verification evidence and manual checks
```

## Troubleshooting

| Problem | What to check |
| --- | --- |
| `php` or `composer` is not recognized | Install the tool/add its folder to PATH, then open a new terminal. |
| Composer reports an unsupported PHP version | Run `where php` and `php -v`; the CLI may be using an older installation. PHP 8.2+ is required. |
| `could not find driver` | Enable `pdo_mysql` for MySQL or `pdo_sqlite` for the default tests in the file shown by `php --ini`. |
| Connection refused / SQLSTATE 2002 | Start MySQL in XAMPP, confirm the port and `.env` settings, then run `php artisan config:clear`. |
| Access denied / SQLSTATE 1045 | Correct the database username/password in `.env`. |
| Unknown database / SQLSTATE 1049 | Create `folio_book_rental` before migrating. |
| No application encryption key | Run `php artisan key:generate`. |
| Missing table | Run `php artisan migrate`; verify the selected database. |
| Blank or unstyled page | Use the URL printed by `php artisan serve`. Check `public/css/app.css`; rebuild with `npm ci` and `npm run build` if needed. |
| Page expired / HTTP 419 | Reload the form, use a consistent host (`127.0.0.1`), and ensure `storage/framework/sessions` is writable. |
| Deleted record is blocked | Records with any rental history are deliberately retained. Use a record with no transactions to demonstrate deletion. |
| No books in the rental selector | All books may be Rented. Record a return or add another book. |
| Port 8000 is busy | Run `php artisan serve --port=8001` and use the displayed URL. |

For other errors, inspect `storage/logs/laravel.log`. PHP must be able to write to `storage` and `bootstrap/cache`.

## Resetting only a disposable demonstration database

**Destructive command:** `php artisan migrate:fresh --seed` drops every table in the configured database before recreating and seeding it. Use it only after checking `.env` and confirming that the selected database is disposable. It is not part of normal startup.

## References and credits

Assignment sources: the supplied **Book Renting System.docx** and **LARAVEL Commands and Sample Project.docx**. The assignment's database diagram and field tables take precedence over the CRUD example's generic `id` column.

- [Laravel 12 release notes and PHP compatibility](https://laravel.com/docs/12.x/releases)
- [Eloquent models, custom primary keys, and timestamps](https://laravel.com/docs/12.x/eloquent)
- [Query builder and pessimistic locking](https://laravel.com/docs/12.x/queries)
- [Database transactions](https://laravel.com/docs/12.x/database)
- [Laravel testing](https://laravel.com/docs/12.x/testing)

Laravel is licensed under MIT. DM Sans and Lora are distributed under the SIL Open Font License; their license files are included in `public/fonts`. The interface's book-cover patterns and decorative book illustration are CSS/SVG artwork, not reproductions of published covers.
