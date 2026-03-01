<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription enregistrée - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { 'primary-green': '#0F3F2E', 'dark-green': '#0B2C21' } } } }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0F3F2E 0%, #0B2C21 100%); } </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-2xl text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-green-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 font-poppins">Inscription enregistrée</h2>
        <p class="mt-3 text-gray-600">{{ $message }}</p>
        <a href="{{ route('login') }}" class="mt-6 inline-block px-6 py-2 rounded-md text-white bg-primary-green hover:bg-dark-green transition">
            Aller à la connexion
        </a>
    </div>
</body>
</html>
