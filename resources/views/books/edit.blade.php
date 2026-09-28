@extends('layouts.app')
@section('title', 'Edit book')
@section('section', 'Books')
@section('content')
    <div class="page-head">
        <div><a href="{{ route('books.index') }}" class="text-link text-xs mb-4"><x-icon name="back" />Back to books</a>
            <h1 class="page-title">Edit book</h1>
            <p class="page-subtitle">SUBEdit book</p>
        </div>
    </div>
    <div class="grid lg:grid-cols-[minmax(0,680px)_1fr] gap-8">
        <form method="POST" action="{{ route('books.update', $book) }}" class="form-card">@csrf @method('PUT')
            @include('books._form')<div class="form-actions"><a href="{{ route('books.show', $book) }}"
                    class="btn btn-secondary">Cancel</a><button class="btn btn-primary"><x-icon name="check" />Save
                    changes</button></div>
        </form>
        <aside class="form-aside pt-3 max-w-xs">
            <div class="stat-icon mb-4"><x-icon name="book" /></div>
            <h2>A well-kept collection</h2>
            <p>Each record represents one physical book. Add a separate record for another copy.</p>
            <p class="mt-4">Availability updates automatically when the book is rented or returned.</p>
            <p class="mt-4">The price records the book’s value in Philippine pesos.</p>
            <p class="mt-6 text-xs">Fields marked * are required.</p>
        </aside>
    </div>
@endsection
