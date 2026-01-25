<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $restaurant->name }}</title>
    <link rel="icon" href="{{ $restaurant->hasMedia('favicon') ? $restaurant->getFirstMediaUrl('favicon') : (isset($settings['site_favicon']) ? Storage::disk('public')->url($settings['site_favicon']) : asset('favicon.ico')) }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- CSS Variables for Theme -->
    <style>
        :root {
            --color-cafe-primary: {{ $settings['primary_color'] ?? '#D4A373' }};
            /* Add other dynamic colors if needed */
        }
    </style>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/theme-4/main.jsx'])
</head>
<body class="antialiased bg-stone-50 text-stone-900">
    <div id="theme-4-root"></div>

    <script>
        window.RESTAURANT_BASE_URL = "/r/{{ $restaurant->slug }}";
        window.RESTAURANT_DATA = @json($restaurantData);
    </script>
</body>
</html>
