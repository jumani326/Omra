@extends('layouts.app')

@section('page-title', $pageTitle ?? 'Messagerie')
@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $pageTitle ?? 'Messagerie avec votre groupe' }}</h1>

    @if(!$group)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-amber-800">
            <p class="font-medium">Aucune conversation disponible</p>
            <p class="text-sm mt-1">
                @role('guide')
                    Vous n'avez pas encore de groupe assigné. La messagerie sera disponible lorsque l'agence vous aura attribué un groupe.
                @else
                    Vous n'êtes pas encore assigné à un groupe. La messagerie avec votre guide sera disponible après attribution par l'agence.
                @endrole
            </p>
        </div>
    @endif

    @if($group)

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden flex flex-col" style="min-height: 420px;">
        {{-- Zone des messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
            @forelse($messages as $msg)
                <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] rounded-2xl px-4 py-2.5 {{ $msg->user_id === auth()->id() ? 'bg-[var(--primary-green)] text-white' : 'bg-gray-100 text-gray-900' }}">
                        <p class="text-xs font-medium opacity-90 mb-0.5">
                            {{ $msg->user->name }}
                            @if($msg->user->guide && $msg->user->guide->group_id === $group->id)
                                <span class="text-[var(--gold-accent)]">(Guide)</span>
                            @endif
                        </p>
                        <p class="text-sm whitespace-pre-wrap break-words">{{ $msg->body }}</p>
                        <p class="text-xs opacity-75 mt-1">{{ $msg->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">Aucun message pour l'instant. Envoyez le premier message.</p>
            @endforelse
        </div>

        {{-- Formulaire d'envoi --}}
        <div class="border-t border-gray-200 p-4 bg-gray-50">
            <form action="{{ auth()->user()->hasRole('guide') ? route('guide.messages.store') : route('pelerin.messages.store') }}" method="POST" class="flex gap-3">
                @csrf
                <textarea
                    name="body"
                    rows="2"
                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-[var(--primary-green)] focus:ring-[var(--primary-green)] text-sm"
                    placeholder="Écrivez votre message..."
                    maxlength="5000"
                    required
                >{{ old('body') }}</textarea>
                <button type="submit" class="self-end px-5 py-2.5 rounded-lg bg-[var(--primary-green)] text-white font-medium hover:opacity-90 transition shrink-0">
                    Envoyer
                </button>
            </form>
            @error('body')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    @endif

    @if(session('success'))
        <p class="text-green-600 text-sm mt-3">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p class="text-red-600 text-sm mt-3">{{ session('error') }}</p>
    @endif
</div>

<script>
    document.getElementById('messages-container')?.scrollTo(0, document.getElementById('messages-container').scrollHeight);
</script>
@endsection
