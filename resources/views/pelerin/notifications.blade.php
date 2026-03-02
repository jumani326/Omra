@extends('layouts.app')

@section('page-title', 'Mes notifications')
@section('page-description', 'Consultez les notifications envoyées par votre agence et le système.')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-poppins">Notifications</h1>
            <p class="text-gray-600 mt-1 text-sm">Suivez les changements importants sur votre dossier (validation de demande, mise à jour de statut, etc.).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pelerin.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Retour à mon espace
            </a>
            @if($notifications->whereNull('read_at')->count() > 0)
            <form action="{{ route('pelerin.notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm">
                    Marquer tout comme lu
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Filtres lu / non lu --}}
    <div class="flex items-center gap-3 border-b border-gray-200 pb-2 text-sm">
        <a href="{{ route('pelerin.notifications.index', ['filter' => 'unread']) }}"
           class="px-3 py-1.5 rounded-full {{ $filter === 'unread' ? 'bg-primary-green text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            Non lues
        </a>
        <a href="{{ route('pelerin.notifications.index', ['filter' => 'read']) }}"
           class="px-3 py-1.5 rounded-full {{ $filter === 'read' ? 'bg-primary-green text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            Lues
        </a>
        <a href="{{ route('pelerin.notifications.index', ['filter' => 'all']) }}"
           class="px-3 py-1.5 rounded-full {{ $filter === 'all' ? 'bg-primary-green text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            Toutes
        </a>
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-10 text-center">
            <svg class="w-14 h-14 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-gray-700 font-medium">Aucune notification pour le moment.</p>
            <p class="text-gray-500 text-sm mt-1">Vous serez notifié ici lorsque votre agence mettra à jour votre dossier.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="bg-white rounded-xl border {{ $notification->read_at ? 'border-gray-100' : 'border-primary-green/30' }} shadow-sm px-4 py-3 flex items-start gap-3">
                    <div class="mt-1">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $notification->read_at ? 'bg-gray-100 text-gray-400' : 'bg-primary-green/10 text-primary-green' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                            </svg>
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm text-gray-900 font-medium">
                                {{ $notification->read_at ? 'Notification' : 'Nouvelle notification' }}
                            </p>
                            <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700 mt-1">{{ $notification->content }}</p>
                        <div class="mt-2 flex items-center gap-3">
                            @if(is_null($notification->read_at))
                                <form action="{{ route('pelerin.notifications.read', $notification) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-primary-green hover:underline">
                                        Marquer comme lue
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded-full bg-gray-100 text-gray-600">
                                    Lu
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($notifications->hasPages())
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

