@extends('layouts.app')
@section('title', 'Welcome a new reader.')
@section('section', 'Borrowers')
@section('content')
    <div class="page-head">
        <div><a href="{{ route('borrowers.index') }}" class="text-link text-xs mb-4"><x-icon name="back" />Back to
                borrowers</a>
            <h1 class="page-title">Welcome a new reader.</h1>
            <p class="page-subtitle">SUBWelcome a new reader.</p>
        </div>
    </div>
    <div class="grid lg:grid-cols-[minmax(0,680px)_1fr] gap-8">
        <form method="POST" action="{{ route('borrowers.store') }}" class="form-card">@csrf @include('borrowers._form')<div
                class="form-actions"><a href="{{ route('borrowers.index') }}" class="btn btn-secondary">Cancel</a><button
                    class="btn btn-primary"><x-icon name="check" />Add borrower</button></div>
        </form>
        <aside class="form-aside pt-3 max-w-xs">
            <div class="stat-icon mb-4"><x-icon name="users" /></div>
            <h2>Start with the reader</h2>
            <p>Register a borrower once, then select them whenever you create a rental.</p>
            <p class="mt-4">Their profile keeps their active rentals and completed transactions together.</p>
            <p class="mt-6 text-xs">Fields marked * are required.</p>
        </aside>
    </div>
@endsection
