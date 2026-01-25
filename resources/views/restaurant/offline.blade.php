<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Offline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
    @if(isset($settings['site_favicon']) && $settings['site_favicon'])
        <link rel="icon" type="image/png" href="{{ Storage::disk('public')->url($settings['site_favicon']) }}">
    @endif
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white shadow-xl rounded-2xl p-10 text-center">
        <div class="mb-6 flex justify-center">
            <div class="p-4 bg-red-50 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Unavailable</h1>
        <p class="text-gray-500 mb-8 leading-relaxed">
            This restaurant is currently in offline mode. Please contact the administrator for assistance.
        </p>

        <div class="space-y-4">
            @if(isset($settings['support_email']) && $settings['support_email'])
                <a href="mailto:{{ $settings['support_email'] }}" class="flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gray-900 hover:bg-gray-800 transition duration-150 ease-in-out w-full">
                    Contact via Email
                </a>
            @endif

            @if(isset($settings['support_phone']) && $settings['support_phone'])
                <div class="flex items-center justify-center gap-2 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span>{{ $settings['support_phone'] }}</span>
                </div>
            @endif

            @if(isset($settings['support_whatsapp']) && !empty($settings['support_whatsapp']))
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm font-medium text-gray-500 mb-3">WhatsApp Support</p>
                    <div class="flex flex-col gap-2">
                        @foreach($settings['support_whatsapp'] as $wa)
                            @if(isset($wa['number']) && $wa['number'])
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa['number']) }}" target="_blank" class="flex items-center justify-center gap-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-3.102-5.385-.9-12.016 4.417-15.087 5.309-3.076 12.012-.9 15.118 4.45 3.095 5.344.896 11.97-4.42 15.051-1.638.948-3.483 1.459-5.362 1.46-1.558-.008-3.09-.345-4.524-.98L.057 24zm6.59-16.76c-.229-.533-2.618-.946-2.618-.946-.617-.11-1.28.31-1.65.753-.404.49-.9.846-.9 2.05 0 2.215 1.572 4.49 1.748 4.793.18.307 3.328 5.488 8.164 6.945 2.872.868 3.518.528 4.293.303.864-.25 1.838-.908 2.03-1.637.202-.75.202-1.928.118-2.146-.088-.215-.27-.33-.532-.476l-2.92-1.397c-.258-.125-.567-.09-.8.156l-1.07 1.13c-.22.253-.59.272-.943.12a8.62 8.62 0 01-3.665-2.625c-.27-.318-.309-.724-.078-1.047l.86-1.037c.22-.266.27-.604.093-.902l-1.332-2.936z"/>
                                    </svg>
                                    <span>{{ $wa['number'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
