<div class="form-stack">
    <div><label for="title" class="field-label">Book title <span class="text-muted">*</span></label><input id="title"
            name="title" class="input" required maxlength="255" value="{{ old('title', $book->title ?? '') }}"
            placeholder="e.g. The Little Prince"
            @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
        @error('title')
            <p id="title-error" class="field-error">{{ $message }}</p>
        @enderror
    </div>
    <div><label for="author" class="field-label">Author <span class="text-muted">*</span></label><input id="author"
            name="author" class="input" required maxlength="255" value="{{ old('author', $book->author ?? '') }}"
            placeholder="Author’s full name"
            @error('author') aria-invalid="true" aria-describedby="author-error" @enderror>
        @error('author')
            <p id="author-error" class="field-error">{{ $message }}</p>
        @enderror
    </div>
    <div class="grid sm:grid-cols-2 gap-5">
        <div><label for="published_year" class="field-label">Publication year <span
                    class="text-muted">*</span></label><input type="number" id="published_year" name="published_year"
                class="input" min="1" max="{{ now()->year }}" required
                value="{{ old('published_year', $book->published_year ?? '') }}" placeholder="e.g. 1943"
                @error('published_year') aria-invalid="true" aria-describedby="published_year-error" @enderror>
            @error('published_year')
                <p id="published_year-error" class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div><label for="price" class="field-label">Book price (₱) <span class="text-muted">*</span></label><input
                type="number" step="0.01" min="0" max="999999.99" id="price" name="price"
                class="input" required value="{{ old('price', $book->price ?? '') }}" placeholder="0.00"
                @error('price') aria-invalid="true" aria-describedby="price-error" @enderror>
            @error('price')
                <p id="price-error" class="field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div><label for="description" class="field-label">Description <span class="text-muted font-normal">·
                Optional</span></label>
        <textarea id="description" name="description" class="input" rows="4" maxlength="5000"
            placeholder="A short introduction to the book…"
            @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description', $book->description ?? '') }}</textarea>
        @error('description')
            <p id="description-error" class="field-error">{{ $message }}</p>
        @enderror
    </div>
</div>
