@props(['book'])
<div class="book-mini cover-{{ $book->book_id % 6 }}" aria-hidden="true">{{ mb_substr($book->title, 0, 1) }}</div>
