<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $restaurant->name)</title>
    <link rel="icon" href="{{ isset($settings['site_favicon']) ? Storage::disk('public')->url($settings['site_favicon']) : asset('favicon.ico') }}">

    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="{{ asset('js/gsap.min.js') }}"></script>
    <style>
        .logo-image {
            height: 3rem; /* 48px */
            width: auto;
            object-fit: contain;
            max-width: 100%;
        }
        .loader-logo {
            height: 6rem; /* 96px */
            width: auto;
            object-fit: contain;
            max-width: 100%;
            animation: zoomInOut 2s infinite ease-in-out;
        }
        @keyframes zoomInOut {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Unique Header & Footer Styles */
        header {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .dark header {
            background-color: rgba(30, 41, 59, 0.95); /* Slate-800 mostly opaque */
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        }

        .header-brand-container {
            display: flex;
            align-items: center;
            gap: 0.75rem; /* gap-3 */
            text-decoration: none;
        }

        .restaurant-name-text {
            font-size: 1.25rem; /* text-xl */
            font-weight: 700; /* font-bold */
            letter-spacing: -0.025em; /* tracking-tight */
            color: #111827; /* text-gray-900 */
        }
        .dark .restaurant-name-text {
            color: #ffffff; /* text-white */
        }
        
        .restaurant-initial {
            color: var(--primary-color, #da3743);
        }

        footer {
            background-color: #f3f4f6; /* Gray-100 */
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            margin-top: 4rem;
            padding-top: 4rem;
            padding-bottom: 4rem;
        }
        .dark footer {
            background-color: #020617; /* Slate-950 (darker than body) */
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Loader Styles */
        #page-loader {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 99999 !important;
            display: flex;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #ffffff !important;
            transition: opacity 0.5s ease;
        }
        .dark #page-loader {
            background-color: #111827 !important; /* gray-900 */
        }

        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .loader-text {
            font-size: 1.5rem; /* 2xl */
            font-weight: 700;
            font-family: var(--font-serif);
            color: #111827;
        }
        .dark .loader-text {
            color: #ffffff;
        }

        .loader-dots {
            display: flex;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }

        .loader-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background-color: var(--primary-color, #da3743);
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(-25%); animation-timing-function: cubic-bezier(0.8,0,1,1); }
            50% { transform: translateY(0); animation-timing-function: cubic-bezier(0,0,0.2,1); }
        }
    </style>
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
            <a href="{{ route('restaurant.index', $restaurant->slug) }}" class="brand-logo header-brand-container">
                @if($restaurant->hasMedia('logo'))
                    <img src="{{ $restaurant->getFirstMediaUrl('logo') }}" alt="{{ $restaurant->name }}" class="logo-image">
                @endif
                <span class="restaurant-name-text">
                    <span class="restaurant-initial">{{ substr($restaurant->name, 0, 1) }}</span>{{ substr($restaurant->name, 1) }}
                </span>
            </a>
            <div class="header-actions">
                <button class="icon-btn" id="theme-toggle">
                    <svg class="sun-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Page Loader -->
    <div id="page-loader">
        <div class="loader-content">
            @if($restaurant->hasMedia('logo'))
                <img src="{{ $restaurant->getFirstMediaUrl('logo') }}" alt="{{ $restaurant->name }}" class="loader-logo">
            @endif
            <h1 class="loader-text">{{ $restaurant->name }}</h1>
            <div class="loader-dots">
                <div class="loader-dot" style="animation-delay: 0s"></div>
                <div class="loader-dot" style="animation-delay: 0.1s"></div>
                <div class="loader-dot" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <main id="main-content">
        <div class="container">
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
             const tl = gsap.timeline();

             // First handle loader
             tl.to('#page-loader', {
                 opacity: 0,
                 duration: 0.5,
                 delay: 0.5,
                 onComplete: () => {
                     document.getElementById('page-loader').style.display = 'none';
                 }
             })
             // Then fade in main content
             .to('#main-content', { 
                 opacity: 1, 
                 duration: 0.8, 
                 ease: "power2.out"
             }, "-=0.2")
             // Then reveal elements
             .fromTo(".gs-reveal", 
                 { y: 20, opacity: 0 },
                 { 
                     y: 0, 
                     opacity: 1, 
                     duration: 0.6, 
                     stagger: 0.1, 
                     ease: "power2.out"
                 }, 
                 "-=0.4"
             );
        });
    </script>
    @stack('scripts')
    <div style="position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 50; display: flex; flex-direction: column; gap: 0.75rem;">
        @if($restaurant->zomato_link)
        <a href="{{ $restaurant->zomato_link }}" target="_blank" rel="noopener noreferrer" style="background-color: white; padding: 0.375rem; border-radius: 9999px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; transition: transform 0.2s; overflow: hidden;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Order on Zomato">
            <img src="{{ asset('images/Zomato_Logo.svg') }}" alt="Zomato" style="width: 100%; height: 100%; object-fit: contain;">
        </a>
        @endif
        @if($restaurant->swiggy_link)
        <a href="{{ $restaurant->swiggy_link }}" target="_blank" rel="noopener noreferrer" style="background-color: white; padding: 0.375rem; border-radius: 9999px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; transition: transform 0.2s; overflow: hidden;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Order on Swiggy">
            <img src="{{ asset('images/Swiggy_logo.svg') }}" alt="Swiggy" style="width: 100%; height: 100%; object-fit: contain;">
        </a>
        @endif
    </div>
</body>
</html>
