@extends('layouts.app')

@section('page-title', 'Gestion des documents')
@section('page-description', 'Documents déposés par les pèlerins')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Documents</h1>
            <p class="text-gray-600 mt-1">Documents déposés par les pèlerins (passeport, photo, certificat médical)</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('documents.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pèlerin</label>
                <select name="pilgrim_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous les pèlerins</option>
                    @foreach($pilgrimsForFilter as $p)
                    <option value="{{ $p->id }}" {{ request('pilgrim_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->first_name }} {{ $p->last_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-green focus:ring-primary-green">
                    <option value="">Tous</option>
                    <option value="passport" {{ request('type') == 'passport' ? 'selected' : '' }}>Passeport</option>
                    <option value="photo" {{ request('type') == 'photo' ? 'selected' : '' }}>Photo</option>
                    <option value="medical_certificate" {{ request('type') == 'medical_certificate' ? 'selected' : '' }}>Certificat médical</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-md hover:bg-dark-green transition">
                    Filtrer
                </button>
                <a href="{{ route('documents.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pèlerin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date dépôt</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documents as $doc)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $doc->pilgrim->first_name }} {{ $doc->pilgrim->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $doc->pilgrim->email ?? '—' }}</div>
                            <a href="{{ route('pilgrims.show', $doc->pilgrim) }}" class="text-xs text-primary-green hover:underline">Voir la fiche</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ ucfirst(str_replace('_', ' ', $doc->type)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $doc->uploaded_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-primary-green hover:underline font-medium">
                                Voir / Télécharger
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Aucun document déposé pour le moment. Les pèlerins déposent leurs pièces depuis leur espace « Documents ».
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            {{ $documents->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
