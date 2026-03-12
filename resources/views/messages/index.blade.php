@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h5>Messages reçus</h5>
        @forelse($messages as $message)
            <div class="border rounded p-3 mb-2">
                <strong>{{ $message->subject }}</strong>
                <p class="mb-0">{{ $message->content }}</p>
            </div>
        @empty
            <p class="text-muted mb-0">Aucun message.</p>
        @endforelse
    </div>
</div>
@endsection