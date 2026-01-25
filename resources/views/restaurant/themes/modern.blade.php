<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $restaurant->name }} | Authentic Dining Experience</title>
    
    <!-- Meta Tags -->
    <meta name="description" content="{{ $restaurant->overview ?? 'Experience authentic flavors in a cozy ambience.' }}">
    <meta property="og:title" content="{{ $restaurant->name }}">
    <meta property="og:description" content="{{ $restaurant->overview ?? 'Authentic flavors, cozy ambience. View our menu and book a table.' }}">
    <meta property="og:image" content="{{ $restaurant->getMedia('hero-images')->first()?->getUrl() }}">
    
    <link rel="icon" href="{{ isset($settings['site_favicon']) ? Storage::disk('public')->url($settings['site_favicon']) : asset('favicon.ico') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Toastr & Select2 CSS (Matching home.blade.php) -->
    
    <!-- Toastr & Select2 CSS (Matching home.blade.php) -->
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <!-- Flatpickr CSS -->

    <style>
        /* Custom Styles */
        .hero-bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ $restaurant->getMedia('hero-images')->first()?->getUrl() ?? "/img/hero.jpg" }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        [x-cloak] { display: none !important; }

        /* Gallery Modal Styles */
        #gallery-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.9);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        #gallery-modal.active {
            display: flex;
            opacity: 1;
        }
        .gallery-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 1rem;
            cursor: pointer;
            border-radius: 50%;
            transition: background 0.3s;
            z-index: 10000;
        }
        .gallery-nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .gallery-prev { left: 1rem; }
        .gallery-next { right: 1rem; }
        .gallery-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 10000;
        }
        .gallery-image-container {
            position: relative;
            max-width: 90vw;
            max-height: 85vh;
        }
        .gallery-modal-image {
            max-width: 100%;
            max-height: 85vh;
            object-fit: contain;
            border-radius: 0.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .gallery-counter {
            position: absolute;
            bottom: -2rem;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }
    </style>
    <!-- Alpine.js -->
</head>
<body class="font-sans text-stone-800 antialiased bg-stone-50">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/95 backdrop-blur-sm shadow-md transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="#" class="font-serif text-3xl font-bold text-amber-600 tracking-tighter">{{ $restaurant->name }}.</a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#home" class="text-stone-600 hover:text-amber-600 font-medium transition-colors">Home</a>
                    <a href="#about" class="text-stone-600 hover:text-amber-600 font-medium transition-colors">About</a>
                    <a href="#menu" class="text-stone-600 hover:text-amber-600 font-medium transition-colors">Menu</a>
                    <a href="#gallery" class="text-stone-600 hover:text-amber-600 font-medium transition-colors">Gallery</a>
                    <a href="#reservation" class="px-5 py-2.5 bg-amber-600 text-white font-medium rounded-full hover:bg-amber-700 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 duration-200">Book Table</a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-stone-600 hover:text-amber-600 focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-stone-100 absolute w-full">
            <div class="px-4 pt-2 pb-6 space-y-2 shadow-lg">
                <a href="#home" class="block px-3 py-3 text-base font-medium text-stone-600 hover:text-amber-600 hover:bg-stone-50 rounded-md">Home</a>
                <a href="#about" class="block px-3 py-3 text-base font-medium text-stone-600 hover:text-amber-600 hover:bg-stone-50 rounded-md">About</a>
                <a href="#menu" class="block px-3 py-3 text-base font-medium text-stone-600 hover:text-amber-600 hover:bg-stone-50 rounded-md">Menu</a>
                <a href="#gallery" class="block px-3 py-3 text-base font-medium text-stone-600 hover:text-amber-600 hover:bg-stone-50 rounded-md">Gallery</a>
                <a href="#reservation" class="block px-3 py-3 text-base font-medium text-amber-600 font-bold hover:bg-stone-50 rounded-md">Book a Table</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative h-screen flex items-center justify-center hero-bg">
        <div class="text-center px-4 max-w-4xl mx-auto" data-testid="hero-content">
            <span class="block text-amber-400 font-serif text-xl md:text-2xl mb-4 tracking-widest uppercase font-bold animate-fade-in-up">Welcome to {{ $restaurant->name }}</span>
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-serif font-bold text-white mb-8 leading-tight text-shadow">
                Taste the <br/><span class="text-amber-500">Authentic</span> Flavors
            </h1>
            <p class="text-lg md:text-xl text-stone-200 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                {{ $restaurant->overview ?? 'Experience a culinary journey where tradition meets modern elegance. Fresh ingredients, expert chefs, and an unforgettable atmosphere.' }}
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#menu" class="px-8 py-4 bg-amber-600 text-white font-bold rounded-full hover:bg-amber-700 transition-all shadow-lg hover:shadow-amber-500/30 text-lg">View Menu</a>
                <a href="#reservation" class="px-8 py-4 bg-white/10 backdrop-blur-md border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-stone-900 transition-all text-lg">Book a Table</a>
            </div>
        </div>
        
        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#about" class="text-white opacity-70 hover:opacity-100 transition-opacity">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative group" data-testid="about-image">
                    <div class="absolute -inset-4 bg-amber-100 rounded-2xl transform -rotate-3 transition-transform group-hover:rotate-0 duration-500"></div>
                    <!-- Use existing photos or defaults -->
                    @php
                        $aboutPhoto = $restaurant->getMedia('photos')->count() > 0 ? $restaurant->getMedia('photos')->first()->getUrl() : '/img/about.jpg';
                    @endphp
                    <img src="{{ $aboutPhoto }}" alt="Our Restaurant" class="relative rounded-xl shadow-2xl w-full object-cover h-[600px] transform group-hover:scale-[1.01] transition-transform duration-500">
                </div>
                
                <div class="space-y-8" data-testid="about-content">
                    <div>
                        <span class="text-amber-600 font-bold tracking-wider uppercase text-sm mb-2 block">Our Story</span>
                        <h2 class="text-4xl md:text-5xl font-serif font-bold text-stone-900 mb-6">Culinary Excellence</h2>
                        <div class="w-20 h-1 bg-amber-500 mb-8"></div>
                    </div>
                    
                    <p class="text-lg text-stone-600 leading-relaxed">
                        {{ $restaurant->overview ?? 'We began with a simple mission: to bring authentic flavors to life using only the freshest, locally sourced ingredients. Our kitchen is a playground for innovation, grounded in traditional techniques.' }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-amber-100 p-2 rounded-full text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-stone-900">Fresh Ingredients</h4>
                                <p class="text-stone-500 text-sm">Farm-to-table produce daily</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-amber-100 p-2 rounded-full text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-stone-900">Cozy Ambience</h4>
                                <p class="text-stone-500 text-sm">Perfect for any occasion</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="py-24 bg-stone-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-amber-600 font-bold tracking-wider uppercase text-sm">Discover Our Menu</span>
                <h2 class="text-4xl md:text-5xl font-serif font-bold text-stone-900 mt-2 mb-4">Savor the Taste</h2>
                <div class="w-24 h-1 bg-amber-500 mx-auto"></div>
            </div>

            @foreach($baseCategories as $baseCategory)
            <div class="mb-20">
                <h3 class="text-3xl font-serif font-bold text-stone-800 mb-8 px-4 border-l-4 border-amber-600 inline-block">
                    {{ $baseCategory->name }}
                </h3>

                <!-- Category Grid -->
                <!-- User requested screenshot style: horizontal scrolling or grid? Screenshot shows Grid. -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    @foreach($baseCategory->menuCategories as $category)
                        <a href="{{ route('restaurant.category', ['slug' => $restaurant->slug, 'categorySlug' => $category->slug]) }}" 
                           class="bg-stone-900 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group relative aspect-square flex flex-col items-center justify-end p-4">
                            
                            @php
                                // Assuming category has image, if not use placeholder or specific logic provided?
                                // User screenshot shows images for categories "Redbull Mocktails", "Shake", etc.
                                // MenuCategory uses Spatie Media Library 'category_image' collection
                                $categoryImage = $category->getMedia('category_image')->first()?->getUrl();
                            @endphp

                            <!-- Background Image with Overlay -->
                            <div class="absolute inset-0 z-0">
                                @if($categoryImage)
                                    <img src="{{ $categoryImage }}" alt="{{ $category->name }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700">
                                @else
                                    <div class="w-full h-full bg-stone-800 flex items-center justify-center opacity-80">
                                        <!-- Placeholder Pattern or Icon -->
                                         <svg class="w-12 h-12 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                            </div>

                            <!-- Content -->
                            <div class="relative z-10 text-center w-full">
                                <h4 class="text-white font-bold text-lg md:text-xl leading-tight mb-2 group-hover:text-amber-400 transition-colors">{{ $category->name }}</h4>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endforeach
            
            @if($baseCategories->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl shadow-sm">
                    <p class="text-gray-500 text-lg">Menu items coming soon.</p>
                </div>
            @endif

        </div>
    </section>

    <!-- Gallery Section -->
    @if($restaurant->getMedia('photos')->count() > 0)
    <section id="gallery" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-amber-600 font-bold tracking-wider uppercase text-sm">Gallery</span>
                <h2 class="text-4xl md:text-5xl font-serif font-bold text-stone-900 mt-2 mb-4">A Glimpse of {{ $restaurant->name }}</h2>
                <div class="w-24 h-1 bg-amber-500 mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 auto-rows-[200px]">
                @foreach($restaurant->getMedia('photos')->take(5) as $index => $photo)
                    @php
                        $classes = 'relative group overflow-hidden rounded-xl';
                        if ($index == 0) $classes .= ' sm:col-span-2 md:col-span-2 md:row-span-2';
                        elseif ($index == 3) $classes .= ' sm:col-span-2 md:col-span-2';
                    @endphp
                    <div class="{{ $classes }} cursor-pointer" onclick="openGallery({{ $index }})">
                        <img src="{{ $photo->getUrl() }}" alt="Gallery {{ $index + 1 }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Reservation Section -->
    <section id="reservation" class="py-24 bg-stone-900 text-white relative">
        <div class="absolute inset-0 opacity-20 bg-cover bg-center bg-fixed" style="background-image: url('{{ $restaurant->getMedia('hero-images')->first()?->getUrl() ?? "/img/hero.jpg" }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-stone-900 via-stone-900/80 to-stone-900/90"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12">
                <span class="text-amber-500 font-bold tracking-wider uppercase text-sm">Reservations</span>
                <h2 class="text-4xl md:text-5xl font-serif font-bold mt-2 mb-4">Book Your Table</h2>
                <div class="w-24 h-1 bg-amber-500 mx-auto"></div>
                <p class="mt-4 text-stone-300">We recommend booking in advance.</p>
            </div>

            <form id="bookingForm" action="{{ route('reservation.store', ['slug' => $restaurant->slug]) }}" method="post" class="bg-white/5 backdrop-blur-lg p-8 md:p-12 rounded-2xl shadow-2xl border border-white/10">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-stone-300 text-sm font-bold mb-2 ml-1">Name</label>
                        <input type="text" name="name" required class="w-full bg-stone-800/50 border border-stone-700 rounded-lg px-4 py-3 text-white placeholder-stone-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-stone-300 text-sm font-bold mb-2 ml-1">Phone</label>
                        <input type="tel" name="phone" required class="w-full bg-stone-800/50 border border-stone-700 rounded-lg px-4 py-3 text-white placeholder-stone-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors" placeholder="+1 (555) 000-0000">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-stone-300 text-sm font-bold mb-2 ml-1">Email</label>
                        <input type="email" name="email" required class="w-full bg-stone-800/50 border border-stone-700 rounded-lg px-4 py-3 text-white placeholder-stone-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors" placeholder="john@example.com">
                    </div>
                    <div>
                        <label class="block text-stone-300 text-sm font-bold mb-2 ml-1">Guests</label>
                        <select name="persons" required id="personSelect" class="w-full bg-stone-800/50 border border-stone-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors appearance-none">
                            <option value="1">1 Person</option>
                            <option value="2">2 People</option>
                            <option value="3">3 People</option>
                            <option value="4">4 People</option>
                            <option value="5">5 People</option>
                            <option value="6">6+ People</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-stone-300 text-sm font-bold mb-2 ml-1">Date</label>
                        <input type="text" id="datepicker" name="date" required value="{{ $date }}" class="w-full bg-stone-800/50 border border-stone-700 rounded-lg px-4 py-3 text-white placeholder-stone-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors color-scheme-dark">
                    </div>
                    <div>
                        <label class="block text-stone-300 text-sm font-bold mb-2 ml-1">Time</label>
                        <select id="timeSlotSelect" name="time" required class="w-full bg-stone-800/50 border border-stone-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors appearance-none md:appearance-auto">
                           <!-- Populated via JS -->
                        </select>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="w-full md:w-auto px-10 py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg transition-colors shadow-lg shadow-amber-600/20 text-lg uppercase tracking-wider">Confirm Reservation</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Contact & Map Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <div>
                        <span class="text-amber-600 font-bold tracking-wider uppercase text-sm">Contact Us</span>
                        <h2 class="text-4xl font-serif font-bold text-stone-900 mt-2 mb-6">Get in Touch</h2>
                        <p class="text-stone-600 text-lg">We'd love to hear from you. Visit us or reach out via phone or email.</p>
                    </div>

                    <div class="space-y-6">
                        @if ($restaurant->address)
                        <div class="flex items-start space-x-4">
                            <div class="bg-amber-100 p-3 rounded-full text-amber-600 mt-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-stone-900 text-lg">Location</h4>
                                <p class="text-stone-600">
                                    {{ $restaurant->address }}<br>
                                    {{ $restaurant->city }}, {{ $restaurant->state }}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if ($restaurant->phone)
                        <div class="flex items-start space-x-4">
                            <div class="bg-amber-100 p-3 rounded-full text-amber-600 mt-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-stone-900 text-lg">Phone</h4>
                                <p class="text-stone-600">{{ $restaurant->phone }}</p>
                            </div>
                        </div>
                        @endif

                        @if ($restaurant->user && $restaurant->user->email)
                        <div class="flex items-start space-x-4">
                            <div class="bg-amber-100 p-3 rounded-full text-amber-600 mt-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-stone-900 text-lg">Email</h4>
                                <p class="text-stone-600">{{ $restaurant->user->email }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="h-[400px] bg-stone-200 rounded-2xl overflow-hidden shadow-lg border border-stone-200 relative group">
                    @php
                        // For the embed, use the address as it is most reliable for the 'q' parameter.
                        // Short links (maps.app.goo.gl) generally do not work in the embed 'q' param.
                        $embedQuery = $restaurant->address . ', ' . $restaurant->city . ', ' . $restaurant->state;
                        
                        // If address is empty but we have a link, we can try to use the link, but it might fail if it's a short link.
                        // A better fallback might be just the city/state or restaurant name.
                        if (empty($restaurant->address) && $restaurant->google_map_link) {
                             // Try to extract something or just use the whole link (unreliable)
                             $embedQuery = $restaurant->google_map_link;
                        }
                    @endphp

                    @if ($embedQuery)
                        <iframe 
                            src="https://maps.google.com/maps?q={{ urlencode($embedQuery) }}&output=embed" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                        
                        {{-- Overlay Button to Open Actual Link --}}
                        @if ($restaurant->google_map_link)
                            <a href="{{ $restaurant->google_map_link }}" target="_blank" class="absolute bottom-4 right-4 bg-white text-amber-600 px-4 py-2 rounded-lg shadow-lg font-bold text-sm transform transition-transform duration-300 hover:scale-105 hover:bg-amber-50 flex items-center gap-2">
                                <span>View on Google Maps</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        @endif
                    @else
                        <div class="w-full h-full flex items-center justify-center text-stone-500">Map not available</div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-stone-900 text-stone-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="#" class="font-serif text-3xl font-bold text-amber-500 tracking-tighter block mb-6">{{ $restaurant->name }}.</a>
                    <p class="text-stone-500 mb-6">Experience the essence of authentic dining.</p>
                </div>

                <div>
                    <h3 class="text-white font-bold uppercase tracking-wider mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="#home" class="hover:text-amber-500 transition-colors">Home</a></li>
                        <li><a href="#about" class="hover:text-amber-500 transition-colors">About Us</a></li>
                        <li><a href="#menu" class="hover:text-amber-500 transition-colors">Menu</a></li>
                        <li><a href="#reservation" class="hover:text-amber-500 transition-colors">Reservations</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold uppercase tracking-wider mb-6">Opening Hours</h3>
                     @php
                        $hoursByDay = collect($restaurantHours ?? [])->groupBy('day_name');
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    @endphp
                    <ul class="space-y-3">
                        @foreach($days as $day)
                             @php $slots = $hoursByDay->get($day); @endphp
                             @if($slots && $slots->count())
                                <li class="flex justify-between">
                                    <span>{{ substr($day, 0, 3) }}:</span> 
                                    <span class="text-white">
                                        {{ \Carbon\Carbon::parse($slots->first()->open_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slots->first()->close_time)->format('g:i A') }}
                                    </span>
                                </li>
                             @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-stone-800 mt-16 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} {{ $restaurant->name }}. All rights reserved. | Product by <a href="https://innomitech.in" class="text-amber-500 hover:text-white transition-colors">InnoMi.Tech</a></p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    
    <script>
        // Mobile Menu Toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
        
        // Close mobile menu on link click
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.add('hidden');
            });
        });

        // Sticky Navbar effect
        const nav = document.querySelector('nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                nav.classList.add('shadow-md');
                nav.classList.replace('h-20', 'h-16');
            } else {
                nav.classList.remove('shadow-md');
                nav.classList.replace('h-16', 'h-20');
            }
        });

        // Reservation Logic (similar to home.blade.php)
        $(document).ready(function() {
             const slug = "{{ $restaurant->slug }}";
             
             // Initialize Select2 if needed, though Tailwind UI select looks okay too. 
             // Logic for fetching time slots
             function fetchTimeSlots(date) {
                $.ajax({
                    url: `/r/${slug}/slots`,
                    method: 'GET',
                    data: { date },
                    success: function(response) {
                        const $timeSlotSelect = $('#timeSlotSelect');
                        $timeSlotSelect.empty();
                        
                        if (response.slots.length > 0) {
                             response.slots.forEach(function(slot) {
                                $timeSlotSelect.append(`<option value="${slot}">${slot}</option>`);
                            });
                        } else {
                            $timeSlotSelect.append('<option disabled selected>No slots available</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching time slots:', xhr);
                    }
                });
            }

            flatpickr("#datepicker", {
                dateFormat: "Y-m-d",
                defaultDate: "today",
                minDate: "today",
                allowInput: true,
                wrap: false,
                onReady: function(selectedDates, dateStr) {
                    fetchTimeSlots(dateStr);
                },
                onChange: function(selectedDates, dateStr) {
                    fetchTimeSlots(dateStr);
                }
            });

            // Form Submit
             const bookingForm = $('#bookingForm');
             bookingForm.on('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(bookingForm[0]);
                const url = bookingForm.attr('action');

                 $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: (data) => {
                        if (data.status === 'success') {
                            toastr.success(data.message || 'Confirmed!');
                            bookingForm[0].reset();
                            fetchTimeSlots($('#datepicker').val());
                        } else {
                            toastr.error(data.message || 'Error');
                        }
                    },
                    error: (xhr) => {
                         let message = 'Reservation failed.';
                         if (xhr.responseJSON && xhr.responseJSON.message) {
                             message = xhr.responseJSON.message;
                         }
                         toastr.error(message);
                    }
                });
             });
        });
    </script>

    <div style="position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 50; display: flex; flex-direction: column; gap: 0.75rem;">
        @if($restaurant->zomato_link)
        <a href="{{ $restaurant->zomato_link }}" target="_blank" rel="noopener noreferrer" style="background-color: white; padding: 0.375rem; border-radius: 9999px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; transition: transform 0.2s; overflow: hidden;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Order on Zomato">
            <img src="{{ asset('images/zomato.png') }}" alt="Zomato" style="width: 100%; height: 100%; object-fit: contain;">
        </a>
        @endif
        @if($restaurant->swiggy_link)
        <a href="{{ $restaurant->swiggy_link }}" target="_blank" rel="noopener noreferrer" style="background-color: white; padding: 0.375rem; border-radius: 9999px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; transition: transform 0.2s; overflow: hidden;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Order on Swiggy">
            <img src="{{ asset('images/swiggy.png') }}" alt="Swiggy" style="width: 100%; height: 100%; object-fit: contain;">
        </a>
        @endif
    </div>
    
    <!-- Gallery Modal Structure & Script -->
    <div id="gallery-modal" onclick="closeGallery()">
        <button class="gallery-close" onclick="closeGallery()">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12"></path>
            </svg>
        </button>
        
        <button class="gallery-nav-btn gallery-prev" onclick="prevGalleryImage(event)">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <div class="gallery-image-container" onclick="event.stopPropagation()">
            <img id="gallery-image" src="" alt="Gallery Image" class="gallery-modal-image">
            <div id="gallery-counter" class="gallery-counter"></div>
        </div>
        
        <button class="gallery-nav-btn gallery-next" onclick="nextGalleryImage(event)">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>

    <script>
        const galleryImages = [
            @foreach($restaurant->getMedia('photos') as $photo)
                "{{ $photo->getUrl() }}",
            @endforeach
        ];
        let currentGalleryIndex = 0;
        const galleryModal = document.getElementById('gallery-modal');
        const galleryImage = document.getElementById('gallery-image');
        const galleryCounter = document.getElementById('gallery-counter');

        function openGallery(index) {
            currentGalleryIndex = index;
            updateGalleryImage();
            galleryModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeGallery() {
            galleryModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function nextGalleryImage(e) {
            if(e) e.stopPropagation();
            currentGalleryIndex = (currentGalleryIndex + 1) % galleryImages.length;
            updateGalleryImage();
        }

        function prevGalleryImage(e) {
            if(e) e.stopPropagation();
            currentGalleryIndex = (currentGalleryIndex - 1 + galleryImages.length) % galleryImages.length;
            updateGalleryImage();
        }

        function updateGalleryImage() {
            galleryImage.src = galleryImages[currentGalleryIndex];
            galleryCounter.innerText = (currentGalleryIndex + 1) + ' / ' + galleryImages.length;
        }

        document.addEventListener('keydown', function(e) {
            if (!galleryModal.classList.contains('active')) return;
            if (e.key === 'Escape') closeGallery();
            if (e.key === 'ArrowRight') nextGalleryImage();
            if (e.key === 'ArrowLeft') prevGalleryImage();
        });
    </script>
</body>
</html>
