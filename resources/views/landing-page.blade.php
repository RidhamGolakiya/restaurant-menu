<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Restaurant SaaS') }} - Book Your Onboarding Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased text-gray-900 bg-white">

    <!-- Sticky Nav -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-sm z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-indigo-600">
                        {{ config('app.name', 'Restaurant SaaS') }}
                    </span>
                </div>
                <div class="hidden md:flex gap-8">
                    <a href="#how-it-works" class="text-gray-600 hover:text-primary-600 transition">How it Works</a>
                    <a href="#benefits" class="text-gray-600 hover:text-primary-600 transition">Benefits</a>
                    <a href="#restaurants" class="text-gray-600 hover:text-primary-600 transition">Our Partners</a>
                    <a href="#faq" class="text-gray-600 hover:text-primary-600 transition">FAQ</a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="https://calendly.com/ridhamgolakiya/30min" target="_blank" class="bg-primary-600 text-white px-5 py-2.5 rounded-full font-medium hover:bg-primary-700 transition shadow-lg shadow-primary-500/30">
                        Book Demo
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-50 text-primary-700 text-sm font-semibold mb-6">
                🚀 Managing 500+ Restaurants Worldwide
            </span>
            <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-gray-900 mb-6 leading-tight">
                Simplify Your Restaurant <br class="hidden md:block">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-indigo-600">
                    Operations & Growth
                </span>
            </h1>
            <p class="mt-4 text-xl text-gray-600 max-w-2xl mx-auto mb-10">
                The all-in-one platform to manage menus, reservations, and orders. 
                Save time and increase revenue with our intuitive solution.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="https://calendly.com/ridhamgolakiya/30min" target="_blank" class="px-8 py-4 bg-primary-600 text-white text-lg font-bold rounded-full hover:bg-primary-700 transition transform hover:scale-105 shadow-xl shadow-primary-500/30">
                    Book a Free Demo
                </a>
                <a href="#how-it-works" class="px-8 py-4 bg-white text-gray-700 border border-gray-200 text-lg font-semibold rounded-full hover:bg-gray-50 transition hover:border-gray-300">
                    See How It Works
                </a>
            </div>
            <p class="mt-4 text-sm text-gray-500">No credit card required · Free 14-day trial</p>
        </div>
    </section>

    <!-- Trusted By -->
    <section class="py-10 bg-gray-50 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-6">Trusted by leading establishments</p>
            <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                <!-- Placeholders for logos -->
                <span class="text-2xl font-bold text-gray-400">RestoBar</span>
                <span class="text-2xl font-bold text-gray-400">The Cafe</span>
                <span class="text-2xl font-bold text-gray-400">BurgerJoint</span>
                <span class="text-2xl font-bold text-gray-400">FineDine</span>
                <span class="text-2xl font-bold text-gray-400">CoffeeHouse</span>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Get Started in 3 Simple Steps</h2>
                <p class="mt-4 text-lg text-gray-600">We make onboarding effortless so you can focus on food.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl font-bold">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Book a Demo</h3>
                    <p class="text-gray-600">Schedule a quick call with our experts to walk through the platform.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl font-bold">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Customize Your Menu</h3>
                    <p class="text-gray-600">Upload your menu items, photos, and configure your restaurant profile.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl font-bold">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Go Live</h3>
                    <p class="text-gray-600">Start accepting orders and reservations immediately from your customers.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Benefits -->
    <section id="benefits" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl mb-6">Why Restaurants Choose Us</h2>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Increase Revenue</h3>
                                <p class="text-gray-600 mt-1">Optimize table turnover and upsell menu items effortlessly.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Save Time</h3>
                                <p class="text-gray-600 mt-1">Automate reservations and streamline kitchen operations.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Better Customer Experience</h3>
                                <p class="text-gray-600 mt-1">Provide seamless digital menus and easy booking for guests.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary-600 to-indigo-600 transform skew-y-6 rounded-3xl opacity-20"></div>
                    <img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Restaurant Dashboard" class="relative rounded-2xl shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants -->
    <section id="restaurants" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold tracking-wider uppercase">Our Community</span>
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl mt-2">Featured Partners</h2>
                <p class="mt-4 text-lg text-gray-600">Join hundreds of successful businesses powering their growth with us.</p>
            </div>

            @if($restaurants->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($restaurants as $restaurant)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition overflow-hidden border border-gray-100 flex flex-col">
                    <div class="h-48 bg-gray-200 relative overflow-hidden">
                        @if($restaurant->hasMedia(\App\Models\Restaurant::HERO_IMAGE))
                            <img src="{{ $restaurant->getFirstMediaUrl(\App\Models\Restaurant::HERO_IMAGE) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                        @else
                             <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                             </div>
                        @endif
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-gray-800 shadow-sm">
                            {{ $restaurant->type ?? 'Restaurant' }}
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 mb-3">
                            @if($restaurant->hasMedia(\App\Models\Restaurant::LOGO))
                                <img src="{{ $restaurant->getFirstMediaUrl(\App\Models\Restaurant::LOGO) }}" alt="Logo" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                                    {{ substr($restaurant->name, 0, 1) }}
                                </div>
                            @endif
                            <h3 class="text-xl font-bold text-gray-900">{{ $restaurant->name }}</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $restaurant->overview ?? 'Experience fine dining and exceptional service.' }}</p>
                        
                        <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-sm text-gray-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $restaurant->city }}, {{ $restaurant->country?->name }}
                            </span>
                            <a href="{{ route('restaurant.index', ['slug' => $restaurant->slug]) }}" class="text-primary-600 font-medium hover:text-primary-700 text-sm flex items-center gap-1">
                                Visit Menu <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center bg-gray-50 rounded-2xl p-10 border border-gray-100">
                <p class="text-gray-500">More partners joining soon!</p>
            </div>
            @endif
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Frequently Asked Questions</h2>
            </div>
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-2">How long does it take to set up?</h3>
                    <p class="text-gray-600">Most restaurants are live within 24 hours. Our team helps you import your menu and configure your settings.</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-2">Is there a free trial?</h3>
                    <p class="text-gray-600">Yes! We offer a 14-day free trial with full access to all features so you can see the value before you commit.</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-2">Can I manage multiple locations?</h3>
                    <p class="text-gray-600">Absolutely. Our platform is built for growth, allowing you to manage single or multiple locations from one dashboard.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section id="demo" class="py-20 bg-indigo-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-4xl font-bold mb-6">Ready to Transform Your Restaurant?</h2>
            <p class="text-xl text-indigo-200 mb-10">Join thousands of restaurateurs who are saving time and growing revenue with our platform.</p>
            <div class="bg-white rounded-2xl p-8 max-w-md mx-auto shadow-2xl">
                 <h3 class="text-gray-900 text-2xl font-bold mb-6">Schedule Your Demo</h3>
                 @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                 @endif
                 <form action="{{ route('demo.store') }}" method="POST" class="space-y-4 text-left">
                     @csrf
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                         <input type="text" name="name" required class="w-full rounded-lg border-gray-300 border p-3 focus:ring-2 focus:ring-primary-500 text-gray-700 focus:border-primary-500 transition" placeholder="John Doe">
                     </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Restaurant Name</label>
                         <input type="text" name="restaurant_name" required class="w-full rounded-lg border-gray-300 border p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-700 transition" placeholder="My Awesome Bistro">
                     </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Work Email</label>
                         <input type="email" name="email" required class="w-full rounded-lg border-gray-300 border p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-700 transition" placeholder="john@restaurant.com">
                     </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                         <input type="text" name="phone" class="w-full rounded-lg border-gray-300 border p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-700 transition" placeholder="+91 72650 25193">
                     </div>
                     <button type="submit" class="w-full bg-primary-600 text-white font-bold py-4 rounded-lg hover:bg-primary-700 transition shadow-lg mt-2">
                         Request Callback
                     </button>
                     <p class="text-xs text-center text-gray-500 mt-4">By booking, you agree to our Terms & Privacy Policy.</p>
                 </form>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-4 gap-8">
            <div class="col-span-2">
                <span class="text-2xl font-bold text-white mb-4 block">{{ config('app.name', 'Restaurant SaaS') }}</span>
                <p class="max-w-xs">Empowering restaurants with the best tools to manage operations, reservations, and customer experiences.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Platform</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white transition">Features</a></li>
                    <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                    <li><a href="#" class="hover:text-white transition">Case Studies</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Company</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white transition">About Us</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact</a></li>
                    <li class="pt-4 text-sm text-gray-500">Contact Us:</li>
                    <li><a href="mailto:innomitech@gmail.com" class="hover:text-white transition">innomitech@gmail.com</a></li>
                    <li><a href="tel:+917265025193" class="hover:text-white transition">+91 72650 25193</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-8 border-t border-gray-800 text-center text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'Restaurant SaaS') }}. All rights reserved.
        </div>
    </footer>

</body>
</html>
