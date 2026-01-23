<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $category->name }} - {{ $restaurant->name }}</title>
    
    <title>{{ $category->name }} - {{ $restaurant->name }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>

    <!-- Alpine.js -->
</head>
<body class="font-sans text-stone-800 antialiased bg-stone-50" x-data="{ productModalOpen: false, activeProduct: null }">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/95 backdrop-blur-sm shadow-md transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Back Button -->
                <a href="{{ route('restaurant.index', $restaurant->slug) }}" class="flex items-center text-stone-600 hover:text-amber-600 transition-colors group">
                    <svg class="w-6 h-6 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-medium">Back to Menu</span>
                </a>

                <!-- Logo/Name -->
                <div class="flex-shrink-0 flex items-center">
                    @if($restaurant->logo)
                        <img class="h-12 w-auto" src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}">
                    @else
                        <h1 class="text-2xl font-serif font-bold text-stone-800">{{ $restaurant->name }}</h1>
                    @endif
                </div>

                <!-- Spacer for balance -->
                <div class="w-24"></div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="pt-32 pb-16 px-4 sm:px-6 lg:px-8 bg-white border-b border-stone-100">
        <div class="max-w-7xl mx-auto text-center">
            <span class="text-amber-600 font-bold tracking-wider uppercase text-sm">{{ $category->baseCategory->name ?? 'Menu' }}</span>
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-stone-900 mt-2 mb-4">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-stone-600 max-w-2xl mx-auto text-lg">{{ $category->description }}</p>
            @endif
        </div>
    </header>

    <!-- Product Grid -->
    <section class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($products as $item)
                    <!-- Product Card -->
                    <div @click="activeProduct = {{ $item->toJson() }}; productModalOpen = true" 
                         class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group cursor-pointer h-full flex flex-col">
                        <div class="h-64 overflow-hidden relative">
                            @php
                                $itemImage = $item->getMedia('menu_image')->first()?->getUrl() ?? null;
                            @endphp
                            @if($itemImage)
                                <img src="{{ $itemImage }}" alt="{{ $item->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-stone-100 flex items-center justify-center text-stone-300">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-amber-600 text-white px-3 py-1 rounded-full font-bold shadow-md transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                {{ $currency->icon ?? '$' }}{{ $item->price }}
                            </div>
                        </div>
                        <div class="p-6 relative flex-1 flex flex-col">
                            <h3 class="font-serif text-2xl font-bold text-stone-900 mb-2">{{ $item->name }}</h3>
                            <p class="text-stone-600 text-sm line-clamp-2 mb-4">{{ $item->ingredients }}</p>
                            <div class="mt-auto text-amber-600 text-sm font-bold flex items-center">
                                View Details <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl shadow-sm">
                    <p class="text-gray-500 text-lg">No items found in this category.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Product Modal -->
    <div x-cloak x-show="productModalOpen" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-stone-900/75 backdrop-blur-sm transition-opacity" 
             x-show="productModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="productModalOpen = false"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl"
                     x-show="productModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="productModalOpen = false">
                    
                    <button @click="productModalOpen = false" class="absolute top-4 right-4 z-20 bg-white/50 hover:bg-white rounded-full p-2 transition-colors">
                        <svg class="w-6 h-6 text-stone-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="grid md:grid-cols-2">
                        <div class="relative h-64 md:h-auto bg-stone-100">
                             <!-- Image handling via Alpine -->
                             <template x-if="activeProduct?.media && activeProduct.media.length > 0">
                                 <img :src="activeProduct?.media[0]?.original_url" alt="Product Image" class="w-full h-full object-cover">
                             </template>
                             <template x-if="!activeProduct?.media || activeProduct.media.length === 0">
                                 <div class="w-full h-full flex items-center justify-center text-stone-300">
                                     <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                 </div>
                             </template>
                        </div>
                        <div class="p-8 md:p-12 flex flex-col justify-center">
                            <span class="text-amber-600 font-bold uppercase tracking-wider text-sm mb-2" x-text="activeProduct?.category?.name || '{{ $category->name }}'"></span>
                            <h3 class="font-serif text-3xl md:text-4xl font-bold text-stone-900 mb-4" x-text="activeProduct?.name"></h3>
                            <div class="text-2xl font-bold text-amber-600 mb-6">
                                {{ $currency->icon ?? '$' }}<span x-text="activeProduct?.price"></span>
                            </div>
                            <p class="text-stone-600 text-lg leading-relaxed mb-6" x-text="activeProduct?.description || activeProduct?.ingredients || 'No description available.'"></p>
                            
                            <!-- Optional Attributes (Example) -->
                            <template x-if="activeProduct?.ingredients">
                                <div class="mt-4 pt-4 border-t border-stone-100">
                                    <h4 class="font-bold text-stone-900 mb-2">Ingredients</h4>
                                    <p class="text-stone-500 text-sm" x-text="activeProduct.ingredients"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
