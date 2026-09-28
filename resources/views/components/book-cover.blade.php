@props(['book', 'size' => 'normal'])
@php
    $coverImage = null;
    $titleRaw = $book->title ?? '';
    $titleClean = str_replace(['’', '‘', '`'], "'", strtolower(trim($titleRaw)));

    // Direct mapping to organized cover images
    $coverMap = [
        'call me by your name' => 'images/covers/CallMeByYourName.jpg',
        'challengers' => 'images/covers/Challengers.jpg',
        'red, white & royal blue' => 'images/covers/Redwhite&RoyalBlue.jpg',
        'red white & royal blue' => 'images/covers/Redwhite&RoyalBlue.jpg',
        'red white and royal blue' => 'images/covers/Redwhite&RoyalBlue.jpg',
        'the great gatsby' => 'images/covers/theGreatGatsby.jpg',
        'the song of achilles' => 'images/covers/TheSongofAchilles.jpg',
        'the hunger games' => 'images/covers/the hunger games.jpg',
        'aristotle and dante discover the secrets of the universe' => 'images/covers/aristotle and dante discover the secrets of the universe.jpg',
        'the seven husbands of evelyn hugo' => 'images/covers/the seven husbands of evelyn hugo.jpg',
        'the invisible life of addie larue' => 'images/covers/the invisible life of adie larue.jpg',
        'the invisible life of adie larue' => 'images/covers/the invisible life of adie larue.jpg',
        'the little prince' => 'images/covers/the little prince.jpg',
        'pride and prejudice' => 'images/covers/pride and prejudice.jpg',
        "a room of one's own" => 'images/covers/a room of ones own.jpg',
        'a room of ones own' => 'images/covers/a room of ones own.jpg',
        'the secret garden' => 'images/covers/The secret garden.jpg',
        'little women' => 'images/covers/Little women.jpg',
        'el filibusterismo' => 'images/covers/El filibusterismo.jpg',
        'noli me tangere' => 'images/covers/Noli Me Tangere.jpg',
        'the art of war' => 'images/covers/The Art of war.jpg',
        'the adventures of tom sawyer' => 'images/covers/The Adventures of Tom Sawyer.jpg',
        'the time machine' => 'images/covers/theTimeMachine.jpg',
    ];

    if (isset($coverMap[$titleClean]) && file_exists(public_path($coverMap[$titleClean]))) {
        $coverImage = asset($coverMap[$titleClean]);
    } else {
        $slug = \Illuminate\Support\Str::slug($titleRaw);
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $titleRaw));
        if (file_exists(public_path("images/covers/{$slug}.jpg"))) {
            $coverImage = asset("images/covers/{$slug}.jpg");
        } elseif (file_exists(public_path("images/covers/{$clean}.jpg"))) {
            $coverImage = asset("images/covers/{$clean}.jpg");
        }
    }
@endphp

@if ($coverImage)
    <div {{ $attributes->merge(['class' => 'book-cover book-cover-photo size-' . $size]) }} aria-hidden="true">
        <div class="cover-spine-edge" aria-hidden="true"></div>
        <img src="{{ $coverImage }}" alt="{{ $book->title }} cover" class="cover-img" loading="lazy">
        <div class="cover-crease" aria-hidden="true"></div>
        <div class="cover-sheen" aria-hidden="true"></div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'book-cover cover-' . ($book->book_id % 6) . ' size-' . $size]) }} aria-hidden="true">
        <div class="cover-spine-edge" aria-hidden="true"></div>
        <div class="cover-content">
            <div class="cover-top-line">
                <span class="cover-label">Folio Archive · {{ $book->published_year }}</span>
                <span class="cover-symbol">✦</span>
            </div>
            <div class="cover-title-wrap">
                <span class="cover-title">{{ $book->title }}</span>
            </div>
            <div class="cover-bottom-line">
                <span class="cover-author">{{ $book->author }}</span>
            </div>
        </div>
        <div class="cover-crease" aria-hidden="true"></div>
    </div>
@endif
