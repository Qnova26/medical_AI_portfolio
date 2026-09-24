<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <title>Medical AI Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gradient-to-br from-blue-100 to-blue-300">

    <div class="min-h-screen flex items-center justify-center p-6">

        <div class="w-full max-w-md">

            @yield('content')

            {{ $slot }}

        </div>

    </div>

</body>
</html>