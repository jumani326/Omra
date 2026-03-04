<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expirée - {{ config('app.name', 'Umrah Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { 'primary-green': '#0F3F2E', 'dark-green': '#0B2C21' } } } }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4 font-['Inter']" style="background: linear-gradient(135deg, #0F3F2E 0%, #0B2C21 100%);">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-2xl text-center">
        <p class="text-6xl font-bold text-primary-green font-['Poppins']">419</p>
        <h1 class="mt-2 text-xl font-semibold text-gray-800">Session expirée</h1>
        <p class="mt-3 text-gray-600 text-sm">
            Votre session a expiré ou le formulaire n’est plus valide. Reconnectez-vous pour continuer.
        </p>
        <a href="{{ route('login') }}" class="mt-6 inline-block w-full py-3 px-4 text-sm font-medium rounded-md text-white bg-primary-green hover:bg-dark-green focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-green transition">
            Retour à la connexion
        </a>
    </div>
</body>
</html>
