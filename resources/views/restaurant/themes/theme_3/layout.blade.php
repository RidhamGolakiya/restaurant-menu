<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $restaurant->name)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="{{ asset('js/gsap.min.js') }}"></script>
    @stack('styles')
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body>
    
    <header>
        <div class="container header-content">
            <a href="{{ route('restaurant.index', $restaurant->slug) }}" class="brand-logo">
                <span class="brand-highlight">{{ substr($restaurant->name, 0, 1) }}</span>{{ substr($restaurant->name, 1) }}
            </a>
            <div class="header-actions">
                <button class="icon-btn" id="theme-toggle">
                    <svg class="sun-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <main id="main-content">
        <div class="container" style="padding-top: 2rem;">
            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3 class="footer-heading">{{ $restaurant->name }}</h3>
                    <p class="footer-text">{{ $restaurant->address }}<br>{{ $restaurant->city }}</p>
                    <p class="footer-text">{{ $restaurant->phone }}</p>
                </div>
                
                @if(!empty($restaurant->social_links))
                <div>
                     <h3 class="footer-heading">Follow Us</h3>
                     <div style="display:flex; gap: 1rem;">
                         @foreach($restaurant->social_links as $platform => $url)
                            <a href="{{ Str::startsWith($url, 'http') ? $url : 'https://' . $url }}" target="_blank" class="icon-btn" aria-label="{{ ucfirst($platform) }}">
                                @if(Str::contains(strtolower($platform), 'instagram'))
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                                @elseif(Str::contains(strtolower($platform), 'facebook'))
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                                @elseif(Str::contains(strtolower($platform), 'twitter') || Str::contains(strtolower($platform), 'x'))
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z" /><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" /></svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                @endif
                            </a>
                         @endforeach
                     </div>
                </div>
                @endif
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} {{ $restaurant->name }}. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const sunIcon = document.querySelector('.sun-icon');
        const moonIcon = document.querySelector('.moon-icon');

        function updateThemeUI() {
            if (document.documentElement.classList.contains('dark')) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }
        updateThemeUI();

        themeToggleBtn.addEventListener('click', () => {
             document.documentElement.classList.toggle('dark');
             localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
             updateThemeUI();
        });

        window.addEventListener('load', () => {
             // First fade in the main container
             gsap.to('#main-content', { 
                 opacity: 1, 
                 duration: 0.8, 
                 ease: "power2.out",
                 onComplete: () => {
                     // Then animate the reveal elements
                     gsap.fromTo(".gs-reveal", 
                         { y: 20, opacity: 0 },
                         { 
                             y: 0, 
                             opacity: 1, 
                             duration: 0.6, 
                             stagger: 0.1, 
                             ease: "power2.out"
                         }
                     );
                 }
             });
        });
    </script>
    @stack('scripts')
</body>
</html>
