<div class="timeline">
    @foreach($trackings as $item)
        <div class="timeline-item mb-3">
            <h6>{{ $item->status ?? 'Update' }}</h6>
            <p>{{ $item->comment }}</p>
            <small>
                By {{ optional($item->user)->name }} — {{ $item->created_at->format('d M Y h:i A') }}
            </small>
            <hr>
        </div>
    @endforeach
</div>
