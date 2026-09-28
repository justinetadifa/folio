@if ($rentals->isEmpty())<x-empty title="No rentals just yet"
        message="New rental transactions will appear here, with a record of each return." icon="rental" />
@else<div class="table-wrap">
        <table class="data-table">
            <caption>Rental history</caption>
            <thead>
                <tr>
                    <th scope="col">Reference</th>
                    <th scope="col">{{ $mode === 'book' ? 'Borrower' : 'Book' }}</th>
                    <th scope="col">Rental date</th>
                    <th scope="col">Return date</th>
                    <th scope="col">Status</th>
                    <th scope="col"><span class="sr-only">Details</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rentals as $rental)
                    <tr>
                        <td class="record-id">R-{{ str_pad($rental->rental_id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="font-medium">{{ $mode === 'book' ? $rental->borrower->name : $rental->book->title }}
                        </td>
                        <td class="whitespace-nowrap text-muted">{{ $rental->rental_date->format('M d, Y') }}</td>
                        <td class="whitespace-nowrap text-muted">{{ $rental->return_date?->format('M d, Y') ?? '—' }}
                        </td>
                        <td><x-status :value="$rental->status" /></td>
                        <td><a href="{{ route('rentals.show', $rental) }}" class="text-link text-xs">Details <x-icon
                                    name="arrow" /></a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
