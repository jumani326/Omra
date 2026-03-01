<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { 'primary-green': '#0F3F2E', 'gold-accent': '#C9A227', 'dark-green': '#0B2C21' } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0F3F2E 0%, #0B2C21 100%); } </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-2xl">
        <div>
            <h2 class="text-center text-2xl font-bold text-gray-900 font-poppins">Créer un compte</h2>
            <p class="mt-2 text-center text-sm text-gray-600">Choisissez le type d'inscription</p>
        </div>

        <div class="space-y-4">
            <a href="{{ route('register.agence') }}" class="flex items-center w-full px-4 py-4 border-2 border-primary-green rounded-lg text-primary-green hover:bg-primary-green hover:text-white transition font-medium">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Inscription Agence
            </a>
            <p class="text-xs text-gray-500 text-center">Réservé aux agences de voyage. Validation par le ministère requise.</p>

            <a href="{{ route('register.pelerin') }}" class="flex items-center w-full px-4 py-4 border-2 border-primary-green rounded-lg text-primary-green hover:bg-primary-green hover:text-white transition font-medium">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Inscription Pèlerin
            </a>
            <p class="text-xs text-gray-500 text-center">Espace personnel. Vous recevrez un lien d'activation par email.</p>
        </div>

        <p class="text-center text-sm text-gray-600">
            Déjà un compte ? <a href="{{ route('login') }}" class="font-medium text-primary-green hover:text-dark-green">Se connecter</a>
        </p>
    </div>
</body>
</html>
