<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mot de passe oublié - {{ config('app.name') }}</title>
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
            <h2 class="text-2xl font-bold text-gray-900 font-poppins">Mot de passe oublié</h2>
            <p class="mt-1 text-sm text-gray-600">Saisissez votre email pour recevoir un lien de réinitialisation.</p>
        </div>

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Adresse email</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}" autofocus
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-green focus:border-primary-green">
            </div>
            <button type="submit" class="w-full py-2 rounded-md text-white bg-primary-green hover:bg-dark-green transition">
                Envoyer le lien
            </button>
        </form>

        <p class="text-center text-sm text-gray-600">
            <a href="{{ route('login') }}" class="text-primary-green hover:underline">Retour à la connexion</a>
        </p>
    </div>
</body>
</html>
