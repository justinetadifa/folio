<div class="form-stack">
    <div><label for="name" class="field-label">Full name <span class="text-muted">*</span></label><input name="name"
            id="name" class="input" maxlength="255" required autocomplete="name"
            value="{{ old('name', $borrower->name ?? '') }}" placeholder="Borrower’s full name"
            @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')
            <p id="name-error" class="field-error">{{ $message }}</p>
        @enderror
    </div>
    <div><label for="contact_number" class="field-label">Contact number <span class="text-muted">*</span></label><input
            name="contact_number" id="contact_number" type="tel" class="input" minlength="7" maxlength="30"
            required autocomplete="tel" value="{{ old('contact_number', $borrower->contact_number ?? '') }}"
            placeholder="e.g. 09XX XXX XXXX"
            @error('contact_number') aria-invalid="true" aria-describedby="contact_number-error" @enderror>
        <p class="field-hint">Include the area or country code when needed. Leading zeros are preserved.</p>
        @error('contact_number')
            <p id="contact_number-error" class="field-error">{{ $message }}</p>
        @enderror
    </div>
</div>
