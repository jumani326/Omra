<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription Agence - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { 'primary-green': '#0F3F2E', 'dark-green': '#0B2C21' } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0F3F2E 0%, #0B2C21 100%); } </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-6 bg-white p-8 rounded-2xl shadow-2xl">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-poppins">Inscription Agence</h2>
            <p class="mt-1 text-sm text-gray-600">Votre compte sera activé après validation par le ministère.</p>
        </div>

        <form action="{{ route('register.agence.store') }}" method="POST" class="space-y-4">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nom du responsable</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-green focus:border-primary-green">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-green focus:border-primary-green">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-green focus:border-primary-green">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-green focus:border-primary-green">
            </div>
            <div>
                <label for="agency_name" class="block text-sm font-medium text-gray-700">Nom de l'agence</label>
                <input id="agency_name" name="agency_name" type="text" required value="{{ old('agency_name') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-green focus:border-primary-green">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                <input id="phone" name="phone" type="text" required value="{{ old('phone') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-green focus:border-primary-green">
            </div>
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Adresse (optionnel)</label>
                <textarea id="address" name="address" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-green focus:border-primary-green">{{ old('address') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('register.choose') }}" class="flex-1 text-center py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Retour</a>
                <button type="submit" class="flex-1 py-2 rounded-md text-white bg-primary-green hover:bg-dark-green transition">
                    S'inscrire
                </button>
            </div>
        </form>

        <p class="text-center text-sm text-gray-600">
            <a href="{{ route('login') }}" class="text-primary-green hover:underline">Se connecter</a>
        </p>
    </div>
</body>
</html>
