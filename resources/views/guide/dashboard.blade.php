@extends('layouts.app')

@section('page-title', 'Mon Groupe')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Mon Groupe</h1>
    @if($group)
        <p class="text-gray-600">Groupe : {{ $group->name }}</p>
        <p class="text-sm text-gray-500">{{ $pilgrims->count() }} pèlerin(s)</p>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Visa</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pilgrims as $p)
                    <tr>
                        <td class="px-4 py-2">{{ $p->first_name }} {{ $p->last_name }}</td>
                        <td class="px-4 py-2">{{ $p->visa->status ?? '—' }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('guide.checkin', $p) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:underline text-sm">Check-in</button>
                            </form>
                            <span class="mx-1">|</span>
                            <form action="{{ route('guide.checkout', $p) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-amber-600 hover:underline text-sm">Check-out</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Check-in / Check-out du jour</h2>
            @forelse($checkinsToday ?? [] as $c)
                <p class="text-sm">{{ $c->type }} — {{ $c->pilgrim->first_name ?? '' }} à {{ $c->created_at->format('H:i') }}</p>
            @empty
                <p class="text-gray-500">Aucun check-in aujourd'hui.</p>
            @endforelse
        </div>
    @else
        <p class="text-gray-500">Aucun groupe assigné.</p>
    @endif
</div>
@endsection
