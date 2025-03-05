<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'PeluAnita') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Ballet:opsz@16..72&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @production
            @php
                $manifest = json_decode(file_get_contents(public_path('dist/manifest.json')));    
            @endphp
            <script type="module" src="/dist/{$manifest['resources/js/app.ts']['file']}"></script>
            <link rel="stylesheet" href="/dist/{$manifest['resources/js/app.ts']['css'][0]}"/>
        @else
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @endproduction
        @inertiaHead
    </head>
    <body class="font-inter antialiased">
        @inertia
    </body>
</html>
