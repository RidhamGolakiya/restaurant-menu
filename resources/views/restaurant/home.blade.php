<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $restaurant->name }} - Restaurant</title>
    {{-- <link rel="icon" type="image/png" href="../assets/images/logo.png" /> --}}
    <link rel="stylesheet" href="{{ asset('css/slick-theme.css') }}" />

    {{-- public/css/slick-theme.css --}}
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/slick.css') }}" />
    <link rel="stylesheet" href="{{ asset('scss/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('scss/home.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Get theme configuration from restaurant
        const themeConfig = @json($restaurant->getThemeConfig());
        
        tailwind.config = {
            theme: {
                fontFamily: {
                    'Poppins': 'Poppins',
                },
                extend: {
                    screens: {
                        'xs': '480px',
                    },
                    colors: {
                        'primary': themeConfig.primary || '#da3743',
                        'secondary': themeConfig.secondary || '#247f9e',
                        'accent': themeConfig.accent || '#f59e0b',
                        'primary-100': themeConfig.primary || '#e15b64',
                        'primary-200': themeConfig.primary || '#8361BD',
                        'primary-300': themeConfig.primary || '#49297F',
                        'black-100': themeConfig.text_color || '#00000099',
                        'black-200': themeConfig.background_color || '#e4e5e7',
                        'gray-100': themeConfig.text_color || '#2d333f',
                        'gray-200': themeConfig.background_color || '#f1f2f4'
                    },
                    backgroundImage: {
                        'hero-img': 'url("../assets/images/hero.png")',
                    },
                },
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body class="antialiased">
    <div id="image-counter" class="hidden md:mr-16 md:ml-8"
        style="position: fixed; top: 20px; right: 70px; color: white; z-index: 1000000;"></div>
    <header
        class="container max-w-[1320px] px-1 md:px-3 mx-auto z-50 w-full py-3 bg-white max-h-20 lg:max-h-[100px] h-full flex items-center"
        id="headerTop">
        <div class="container mx-auto w-full max-w-[1320px] px-3 relative z-10">
            <div class="flex justify-between items-center">
                <a href="{{ route('restaurant.index', ['slug' => $restaurant->slug]) }}"
                    class="block h-screen w-screen max-h-[42px] lg:max-h-[48px] flex items-center">
                    <span class="ml-2 text-2xl font-bold">{{ $restaurant->name }}</span>
                </a>
                @if (auth()->check() && auth()->user()->hasRole('restaurant'))
                    <div class="flex gap-x-3 lg:gap-x-5 items-center justify-end w-full">
                        <a class="text-sm lg:block font-medium text-white bg-secondary hover:bg-white hover:text-secondary border border-secondary rounded-[30px] py-2 xs:py-3 px-4 xs:px-6 transition-all duration-700"
                            href="{{ auth()->check() ? (auth()->user()->hasRole('admin') ? route('filament.admin.pages.dashboard') : route('filament.restaurant.pages.dashboard')) : route('filament.restaurant.auth.login') }}"
                            target="_blank">
                            {{ auth()->check() ? 'Dashboard' : 'Sign in' }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- hero-section -->
    <section>
        <div
            class="bg-[url('{{ $restaurant->getMedia('hero-images')->first()?->getUrl() ?: asset('images/default-hero.jpg') }}')] bg-no-repeat bg-cover max-h-[450px] h-screen relative bg-center">
            <div class="container max-w-[1320px] px-3 w-full max-w-[1464px] mx-auto">
                <div class="lg:basis-1/2">
                    @if ($restaurant->getMedia('photos')->count() > 0)
                        <button data-open-photo-modal
                            class="text-sm w-fit bottom-4 font-medium text-white bg-black-100 hover:bg-primary-100 rounded-[30px] py-2 xs:py-3 px-4 xs:px-6 transition-all duration-700 absolute end-0 start-0 mx-auto">See
                            all photos ( {{ $restaurant->getMedia('photos')->count() }} )</button>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- review-section -->
    <section class="mx-auto">
        <div class="container px-3 w-full max-w-[1320px] mx-auto py-5">
            <div class="lg:flex justify-between gap-10">
                <div class="review-title lg:w-[60%]">
                    {{-- <h2 class="text-[30px] sm:text-[32px] lg:text-[40px] font-bold mb-2">{{ $restaurant->name }}</h2> --}}
                    {{-- <div class="text-[22px] font-medium flex items-center gap-x-4 flex-nowrap">
                        <i class="fa-solid fa-star text-sm text-primary"></i>
                        <ul class="flex gap-x-2 list-disc list-inside flex-wrap">
                            <li class="text-gray-100 text-sm">4.6 (3181)</li>
                            <li class="text-gray-100 text-sm">De $41 a $100</li>
                            <li class="text-gray-100 text-sm">Brasileña</li>
                        </ul>
                    </div> --}}
                    <ul class="flex gap-x-5 py-5 review-link">
                        @php
                            $activeLink = '';
                            if ($restaurant->overview) {
                                $activeLink = 'overview';
                            } elseif ($restaurant->getMedia('photos')->count() > 0) {
                                $activeLink = 'photo';
                            } elseif ($menus && $menus->count() > 0) {
                                $activeLink = 'menu';
                            }
                        @endphp

                        @if ($restaurant->overview)
                            <a href="#overview"
                                class="text-black hover:text-primary active text-lg font-medium transition-all nav-link {{ $activeLink == 'overview' ? 'active' : '' }} duration-700 active:text-primary active:font-semibold">
                                Overview
                            </a>
                        @endif

                        @if ($restaurant->getMedia('photos')->count() > 0)
                            <li><a href="#photo"
                                    class="text-black hover:text-primary text-lg font-medium transition-all nav-link {{ $activeLink == 'photo' ? 'active' : '' }} duration-700">Photos</a>
                            </li>
                        @endif

                        @if ($menus && $menus->count() > 0)
                            <li><a href="#menu"
                                    class="text-black hover:text-primary text-lg font-medium transition-all  nav-link {{ $activeLink == 'menu' ? 'active' : '' }} duration-700">Menu</a>
                            </li>
                        @endif
                    </ul>

                    @if ($restaurant->overview)
                        <div class="overview-section mb-5" id="overview">
                            {{-- <h3>About {{ $restaurant->name }}</h3> --}}
                            <span>
                                {{ $restaurant->overview }}
                            </span>
                        </div>
                    @endif

                    @if ($restaurant->getMedia('photos')->count() > 0)
                        <div class="photo-section mb-3" id="photo">
                            <h3 class="text-[22px] font-bold mb-5">{{ $restaurant->getMedia('photos')->count() }}
                                photos
                            </h3>

                            @php
                                $photos = $restaurant->getMedia('photos');
                            @endphp

                            <div class="photo-slider" data-photo-count="{{ $photos->count() }}">
                                <div class="grid grid-cols-2 gap-3 max-w-4xl mx-auto">
                                    @if ($photos->count())
                                        {{-- First photo (big column) --}}
                                        <a href="{{ $photos[0]->getUrl() }}" class="glightbox" data-gallery="gallery1">
                                            <img src="{{ $photos[0]->getUrl() }}" alt="Photo 1"
                                                class="w-full h-full object-cover rounded-md" loading="lazy" />
                                        </a>

                                        {{-- Remaining photos in a nested grid --}}
                                        @if ($photos->count() > 1)
                                            <div class="grid grid-cols-2 gap-3">
                                                @foreach ($photos->slice(1, 5) as $photo)
                                                    <a href="{{ $photo->getUrl() }}" class="glightbox"
                                                        data-gallery="gallery1">
                                                        <img src="{{ $photo->getUrl() }}" alt="Photo"
                                                            class="w-full h-full object-cover rounded-md"
                                                            loading="lazy" />
                                                    </a>
                                                @endforeach

                                                @if ($photos->count() > 6)
                                                    <a href="{{ $photos[6]->getUrl() }}" class="glightbox relative"
                                                        data-gallery="gallery1">
                                                        <img src="{{ $photos[6]->getUrl() }}" alt="Photo"
                                                            class="w-full h-full object-cover rounded-md"
                                                            loading="lazy" />
                                                        <div
                                                            class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center rounded-md">
                                                            <span class="text-white text-lg font-semibold">
                                                                +{{ $photos->count() - 6 }} more
                                                            </span>
                                                        </div>
                                                    </a>

                                                    @foreach ($photos->slice(7) as $photo)
                                                        <a href="{{ $photo->getUrl() }}" class="glightbox hidden"
                                                            data-gallery="gallery1"></a>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($menus && $menus->count() > 0)
                        <div class="menu-section" id="menu">
                            <h3 class="text-[22px] font-bold mb-5">Menu</h3>
                            @php
                                $filteredCategories = $menuCategories->filter(fn($cat) => $cat->menuItems->count() > 0);
                            @endphp

                            <div x-data="{
                                tab: '{{ $filteredCategories->first()?->name ?? '' }}',
                                expanded: false
                            }" class="w-full">

                                <!-- Tab Buttons -->
                                <div class="flex flex-wrap gap-4 border-b pb-4">
                                    <template x-for="menuCategory in {{ $filteredCategories->toJson() }}"
                                        :key="menuCategory.name">
                                        <button @click="tab = menuCategory.name; expanded = false"
                                            :class="tab === menuCategory.name ? 'border-primary text-primary' :
                                                'border-gray-300 text-gray-700'"
                                            class="px-4 py-2 border rounded transition-all duration-300 text-sm capitalize flex items-center flex-1 md:flex-initial md:max-w-xs"
                                            x-text="menuCategory.name + ' menu'">
                                        </button>
                                    </template>
                                </div>

                                <!-- Tab Content -->
                                <div class="mt-6">
                                    @foreach ($filteredCategories as $menuCategory)
                                        <div x-show="tab === '{{ $menuCategory->name }}'" x-data="{ expanded: false, menuItemsCount: {{ $menuCategory->menuItems->count() }} }"
                                            x-transition>
                                            <!-- Expandable Content -->
                                            <div :class="expanded ? 'max-h-[9999px]' : 'max-h-[400px] md:max-h-[600px]'"
                                                class="overflow-hidden transition-all duration-700 ease-in-out">
                                                <div class="grid grid-cols-2 gap-4 mb-5">
                                                    @foreach ($menuCategory->menuItems as $index => $menu)
                                                        @php
                                                            $isLastOdd = $loop->last && $loop->count % 2 !== 0;
                                                        @endphp
                                                        <div @class([
                                                            'col-span-2 flex justify-center' => $isLastOdd,
                                                            'flex' => !$isLastOdd,
                                                        ])>
                                                            <div
                                                                class="flex flex-col justify-between w-full max-w-sm border border-gray-300 rounded-lg py-2 px-3 bg-white h-full">
                                                                <div>
                                                                    <div
                                                                        class="flex flex-nowrap md:flex-wrap sm:flex-row flex-col gap-md-4 gap-2 justify-between items-start mb-4">
                                                                        <p class="text-md text-gray-600 font-bold">
                                                                            {{ $menu->name }}
                                                                        </p>
                                                                        <span
                                                                            class="inline-block px-2 py-1 text-xs font-semibold border border-red-300 bg-red-500/20 text-primary rounded-lg whitespace-nowrap">
                                                                            {{ $currency->icon ?? '$' }}
                                                                            {{ $menu->price }}
                                                                        </span>
                                                                    </div>
                                                                    <p class="text-sm text-gray-100 mb-2">
                                                                        {{ $menu->ingredients }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Toggle Button -->
                                            <div class="text-center mt-4"
                                                x-show="menuItemsCount > 8 || window.innerWidth < 768"
                                                x-init="$nextTick(() => { menuItemsCount = {{ $menuCategory->menuItems->count() }} })">
                                                <button @click="expanded = !expanded"
                                                    class="px-4 py-2 border rounded text-sm text-primary hover:underline transition-all duration-300"
                                                    x-text="expanded ? 'Show Less' : 'Show More'">
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    @endif

                    {{-- Other Restaurants --}}
                    {{-- @if ($otherRestaurants && $otherRestaurants->count() > 0)
                        <div class="px-10 lg:px-0 mt-5">
                            <h3 class="text-[22px] font-bold mb-5">Explore Our Other Locations</h3>
                            <div class="restaurant-slider">
                                @foreach ($otherRestaurants as $otherRestaurant)
                                    <div class="h-full">
                                        <div
                                            class="h-full restaurant-card w-full border border-gray-300 rounded-lg overflow-hidden group-hover:shadow-lg flex flex-col">
                                            <div
                                                class="restaurant-img h-[150px] w-full group-hover:scale-105 transition-all duration-700">
                                                <img src="{{ $otherRestaurant->getMedia('hero-images')->first()?->getUrl() ?: asset('images/default-hero.jpg') }}"
                                                    alt="{{ $otherRestaurant->name }}"
                                                    class="h-full w-full object-cover" />
                                            </div>
                                            <div class="p-4 mt-2 flex flex-col flex-grow">
                                                <h3 class="text-lg font-bold text-black mb-1 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="w-4 h-4 text-gray-700" viewBox="0 0 512 512">
                                                        <path
                                                            d="M63.9 14.4C63.1 6.2 56.2 0 48 0s-15.1 6.2-16 14.3L17.9 149.7c-1.3 6-1.9 12.1-1.9 18.2 0 45.9 35.1 83.6 80 87.7L96 480c0 17.7 14.3 32 32 32s32-14.3 32-32l0-224.4c44.9-4.1 80-41.8 80-87.7 0-6.1-.6-12.2-1.9-18.2L223.9 14.3C223.1 6.2 216.2 0 208 0s-15.1 6.2-15.9 14.4L178.5 149.9c-.6 5.7-5.4 10.1-11.1 10.1-5.8 0-10.6-4.4-11.2-10.2L143.9 14.6C143.2 6.3 136.3 0 128 0s-15.2 6.3-15.9 14.6L99.8 149.8c-.5 5.8-5.4 10.2-11.2 10.2-5.8 0-10.6-4.4-11.1-10.1L63.9 14.4zM448 0C432 0 320 32 320 176l0 112c0 35.3 28.7 64 64 64l32 0 0 128c0 17.7 14.3 32 32 32s32-14.3 32-32l0-448c0-17.7-14.3-32-32-32z" />
                                                    </svg>
                                                    {{ $otherRestaurant->name }}
                                                </h3>

                                                <p class="text-sm text-gray-800 mb-1 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-4 h-4 text-orange-700">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                    </svg>
                                                    {{ $otherRestaurant->address }}
                                                </p>

                                                <p class="text-sm text-gray-800 mb-2 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-4 h-4 text-green-700">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                                    </svg>
                                                    {{ $otherRestaurant->phone }}
                                                </p>

                                                <div class="flex justify-center mt-auto">
                                                    <a href="{{ $otherRestaurant->slug ? route('restaurant.index', $otherRestaurant->slug) : 'javascript:void(0);' }}"
                                                        class="text-white bg-orange-600 hover:bg-orange-700 transition rounded-md px-4 py-2 font-medium">
                                                        Explore Now
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif --}}
                </div>
                <div class="review-form lg:w-[40%] mt-5 lg:mt-0">
                    <div class="review-card border border-gray-300 p-3 xs:p-5 rounded-lg mb-3 top-0 z-[1] bg-white">
                        <h2 class="text-lg font-semibold pb-4 text-center">Make a reservation</h2>
                        <form id="bookingForm" class="space-y-4"
                            action="{{ route('reservation.store', ['slug' => $restaurant->slug]) }}" method="post">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                {{-- No. of Persons --}}
                                <div class="w-full relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <input type="number" id="personSelect" name="persons"
                                        class="block w-full bg-gray-200 pl-10 pr-10 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="No. of Persons" min="0" required>
                                    <div id="persons-error" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>

                                {{-- Date --}}
                                <div class="w-full relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black">
                                        <i class="fa-solid fa-calendar"></i>
                                    </div>
                                    <input id="datepicker" type="date" value="{{ $date }}"
                                        name="date"
                                        class="w-full bg-gray-200 pl-10 pr-10 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" />
                                    <div id="date-error" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                {{-- Open/Close Status --}}
                                <div
                                    class="flex items-start sm:items-center bg-gray-200 p-2 gap-2 rounded-lg border border-gray-300 w-full">
                                    <svg viewBox="0 0 24 24" class="w-6 h-6 min-w-5"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18ZM11 8.5v4a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5Zm.5 6.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z"
                                            fill="#2D333F">
                                        </path>
                                    </svg>
                                    <p id="status" class="text-sm font-semibold text-gray-600 leading-snug"></p>
                                </div>

                                {{-- Time Slot --}}
                                <div class="w-full time-slot-select">
                                    <select id="timeSlotSelect" name="time"
                                        class="select2  w-full bg-gray-200 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary px-3 py-2">
                                    </select>
                                    <div id="time-error" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                {{-- Customer Name --}}
                                <div class="w-full relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <input type="text" id="customerName" name="name" required
                                        class="block w-full bg-gray-200 pl-10 pr-10 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="Customer Name" />
                                </div>

                                {{-- Email --}}
                                <div class="w-full relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <input type="email" id="customerEmail" name="email" required
                                        class="block w-full bg-gray-200 pl-10 pr-10 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="Email" />
                                </div>
                            </div>

                            <div class="mb-5">
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <input type="number" id="customerPhone" name="phone" required min="0"
                                        class="block w-full bg-gray-200 pl-10 pr-10 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="Phone Number" />
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="slot-buttons flex justify-center">
                                <button type="submit"
                                    class="flex gap-2 bg-white text-primary hover:bg-black-200 transition-all duration-300 border border-primary px-3 py-2 text-sm font-semibold rounded-lg mb-4">
                                    <svg viewBox="0 0 24 24" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 3c1.243 0 2.25.921 2.25 2.057 0 .258-.052.505-.147.732 2.858.64 4.39 2.669 4.617 5.624 1.456 1.492 2.28 3.424 2.28 5.473 0 .568-.504 1.028-1.125 1.028H4.125c-.621 0-1.125-.46-1.125-1.028 0-2.049.824-3.98 2.279-5.473l.024-.262c.296-2.813 1.821-4.742 4.593-5.363a1.897 1.897 0 0 1-.146-.73C9.75 3.92 10.757 3 12 3Zm0 4.629c-3.048 0-4.39 1.435-4.495 4.248a.988.988 0 0 1-.321.685c-.97.903-1.612 2.048-1.84 3.295h13.313l-.06-.286c-.27-1.135-.885-2.175-1.781-3.008a.988.988 0 0 1-.322-.686C16.389 9.064 15.048 7.629 12 7.629ZM14.25 18.943C14.25 20.079 13.243 21 12 21s-2.25-.92-2.25-2.057h4.5Z"
                                            fill="#da3743"></path>
                                    </svg>
                                    Book Appointment
                                </button>
                            </div>
                        </form>


                    </div>
                    <div class="border border-gray-300 p-5 rounded-lg mb-5">
                        @if ($restaurant->google_map_link)
                            @php
                                $googleMapLink = explode('/', $restaurant->google_map_link);
                            @endphp
                            <div class="w-full h-[300px] mb-3">
                                <iframe class="h-full w-full mb-5"
                                    src="https://maps.google.com/maps?q={{ $googleMapLink[5] ?? '' }}&output=embed"
                                    width="600" height="450" style="border:0;" allowfullscreen=""
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                            </div>
                        @endif
                        @if ($restaurant->address)
                            <div class="flex gap-2 items-center">
                                <svg viewBox="0 0 24 24" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 7a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm1 3a1 1 0 1 0-2 0 1 1 0 0 0 2 0Z"
                                        fill="#2D333F"></path>
                                    <path
                                        d="M4 10a8 8 0 1 1 16 0c0 2.813-2.433 6.59-7.3 11.33a1 1 0 0 1-1.4 0C6.433 16.59 4 12.813 4 10Zm14 0a6 6 0 0 0-12 0c0 1.21.8 4 6 9.21 5.2-5.21 6-8 6-9.21Z"
                                        fill="#2D333F"></path>
                                </svg>
                                @if ($restaurant->google_map_link)
                                    <a href="{{ $restaurant->google_map_link }}" class="text-sm text-primary"
                                        target="_blank">
                                        {{ $restaurant->address }},
                                        @if ($restaurant->address_2)
                                            {{ $restaurant->address_2 }},
                                        @endif
                                        {{ $restaurant->city }},
                                        {{ $restaurant->state }},
                                        {{ $restaurant->zip_code }},
                                        {{ $restaurant->country->name }}
                                    </a>
                                @else
                                    <span class="text-sm text-primary"> {{ $restaurant->address }},
                                        @if ($restaurant->address_2)
                                            {{ $restaurant->address_2 }},
                                        @endif
                                        {{ $restaurant->city }},
                                        {{ $restaurant->state }},
                                        {{ $restaurant->zip_code }},
                                        {{ $restaurant->country->name }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="business-hours">
                        <div class="flex gap-2 p-2 border border-gray-300 mb-3 rounded-lg justify-center">
                            <svg viewBox="0 0 24 24" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-5Z"
                                    fill="#2D333F"></path>
                                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-2 0a7 7 0 1 0-14 0 7 7 0 0 0 14 0Z"
                                    fill="#2D333F"></path>
                            </svg>
                            <p class="text-base font-semibold">Business Hours</p>
                        </div>
                        @php
                            $hoursByDay = collect($restaurantHours)->groupBy('day_name');
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
                        <div class="flex gap-2 p-2 border border-gray-300 mb-3 rounded-lg">
                            <table class="mx-auto rounded-lg w-full max-w-xl">
                                <tbody>
                                    @foreach ($days as $day)
                                        @php
                                            $slots = $hoursByDay->get($day);
                                        @endphp
                                        <tr class="align-top">
                                            <td class="text-nowrap text-base font-semibold pr-2 py-2 pl-4 w-32 pt-3">
                                                {{ $day }}</td>
                                            <td class="pe-2 py-2 pt-3">:</td>
                                            <td class="py-2 pr-4">
                                                @if ($slots && $slots->count())
                                                    @foreach ($slots as $slot)
                                                        @php
                                                            $openTime = \Carbon\Carbon::createFromFormat(
                                                                'H:i:s',
                                                                $slot->open_time,
                                                            )->format('g:i A');
                                                            $closeTime = \Carbon\Carbon::createFromFormat(
                                                                'H:i:s',
                                                                $slot->close_time,
                                                            )->format('g:i A');
                                                        @endphp
                                                        <div class="mb-1 flex items-center gap-2">
                                                            <span
                                                                class="border border-gray-300 rounded px-2 py-0.5 text-sm text-center w-full py-2">{{ $openTime }}</span>
                                                            <span>to</span>
                                                            <span
                                                                class="border border-gray-300 rounded px-2 py-0.5 text-sm text-center w-full py-2">{{ $closeTime }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div
                                                        class="border border-gray-300 rounded px-2 py-0.5 text-sm text-center py-2">
                                                        Closed</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="photoModal"
        class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden items-center justify-center p-4 overflow-y-auto">
        <div class="photo-modal bg-white rounded-lg max-w-4xl w-full p-6 relative shadow-lg">
            <button id="closeModalBtn"
                class="absolute top-3 right-6 text-gray-500 hover:text-black text-xl font-bold"><i
                    class="fa-solid fa-xmark"></i></button>
            <h2 class="text-xl font-semibold mb-4">{{ $restaurant->getMedia('photos')->count() }} Photos</h2>

            <div class="flex flex-wrap gap-3 h-[700px] overflow-auto">
                <div
                    class="grid {{ count($restaurant->getMedia('photos')) < 2 ? '' : 'grid-cols-2' }} {{ count($restaurant->getMedia('photos')) < 3 ? '' : 'md:grid-cols-3' }} gap-3 w-full">
                    @foreach ($restaurant->getMedia('photos') as $photo)
                        <a href="{{ $photo->getUrl() }}" class="glightbox-modal" data-gallery="gallery1">
                            <img src="{{ $photo->getUrl() }}" alt="Photo"
                                class="w-full h-full object-cover rounded-md" loading="lazy" />
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
    <!-- Modal -->
    {{-- <div id="bookingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <!-- Close Button -->
            <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-xl font-semibold mb-4 text-center">Book an Appointment</h2>

            <form id="bookingForm" class="space-y-4"
                action="{{ route('reservation.store', ['slug' => $restaurant->slug]) }}" method="post">
                @csrf
                <div>
                    <input type="hidden" name="date" id="BookingDate">
                    <input type="hidden" name="time" id="BookingTime">
                    <input type="hidden" name="persons" id="Persons">
                    <label class="block text-sm font-medium text-gray-700">Customer Name:<span
                            class="text-red-500">*</span></label>
                    <input type="text" id="customerName" name="name" required
                        class="mt-1 block w-full border text-sm border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-600"
                        placeholder="Your name" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email:<span
                            class="text-red-500">*</span></label>
                    <input type="email" id="customerEmail" name="email"
                        class="mt-1 block w-full border text-sm border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-600"
                        placeholder="Your email" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone:<span
                            class="text-red-500">*</span></label>
                    <input type="number" id="customerPhone" name="phone" required min="0"
                        class="mt-1 block w-full border text-sm border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-600"
                        placeholder="Your phone number" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Booking Information:</label>
                    <div id="bookingDetails" class="mt-1 text-sm text-gray-800 font-semibold">
                        <!-- Content will be inserted dynamically -->
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition">
                    Book Appointment
                </button>
            </form>
        </div>
    </div> --}}
    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/slick.min.js') }}"></script>
    <script src="{{ asset('js/index.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navLinks = document.querySelectorAll(".review-link .nav-link");

            navLinks.forEach(link => {
                link.addEventListener("click", function() {
                    navLinks.forEach(l => l.classList.remove("active"));

                    this.classList.add("active");
                });
            });
        });
    </script>

    <script>
        const slider = document.querySelector('.photo-slider');
        const photoCount = parseInt(slider.dataset.photoCount || '0');

        function updateCounter(currentIndex, total) {
            const counter = document.getElementById('image-counter');

            if (counter) {

                counter.innerText = `${(currentIndex%total) + 1} of ${total}`;
            }
        }
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            zoomable: true,
            loop: false,
            autoplayVideos: false,
            moreText: '',
            plyr: {
                css: '',
                js: ''
            },
            openEffect: 'zoom',
            closeEffect: 'zoom',
            onOpen: () => {
                if (photoCount <= 1) {
                    // Hide prev/next navigation if only one image
                    setTimeout(() => {
                        document.querySelector('.gprev')?.classList.add('hidden');
                        document.querySelector('.gnext')?.classList.add('hidden');
                    }, 100);
                }
                document.getElementById('image-counter').classList.remove('hidden');
                updateCounter(lightbox.index, photoCount);
            },
            onClose: () => {
                document.getElementById('image-counter').classList.add('hidden');
            },
            afterSlideChange: () => {
                updateCounter(lightbox.index, photoCount);
            }
        });

        const modalLightbox = GLightbox({
            selector: '.glightbox-modal',
            touchNavigation: true,
            zoomable: true,
            loop: false,
            autoplayVideos: false,
            moreText: '',
            plyr: {
                css: '',
                js: ''
            },
            openEffect: 'zoom',
            closeEffect: 'zoom',
            onOpen: () => {
                if (photoCount <= 1) {
                    // Hide prev/next navigation if only one image
                    setTimeout(() => {
                        document.querySelector('.gprev')?.classList.add('hidden');
                        document.querySelector('.gnext')?.classList.add('hidden');
                    }, 100);
                }
                document.getElementById('image-counter').classList.remove('hidden');

                updateCounter(modalLightbox.index, photoCount);
            },
            onClose: () => {
                document.getElementById('image-counter').classList.add('hidden');
            },
            afterSlideChange: () => {
                updateCounter(modalLightbox.index, photoCount);
            }
        });
    </script>
    <script>
        flatpickr("#datepicker", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            minDate: "today",
            allowInput: true,
            wrap: false
        });

        const links = document.querySelectorAll('.nav-link');

        links.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const href = link.getAttribute('href');
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
    <script>
        function fetchTimeSlots(date) {
            $.ajax({
                url: `/r/${slug}/slots`,
                method: 'GET',
                data: {
                    date
                },
                success: function(response) {
                    const $timeSlotSelect = $('#timeSlotSelect');
                    const $status = $('#status');
                    $timeSlotSelect.empty();

                    if (response.slots.length > 0) {
                        $status.text('Restaurant Open');
                        $timeSlotSelect.append('<option disabled selected></option>');
                        response.slots.forEach(function(slot) {
                            $timeSlotSelect.append(
                                `<option value="${slot}">${slot}</option>`);
                        });
                        $('#timeSlotSelect').select2({
                            placeholder: "Select a time slot",
                            allowClear: true
                        });
                        $('#timeSlotSelect').next('.select2-container').removeClass(
                            'no-slots');
                    } else {
                        $status.text('Restaurant Closed');
                        $timeSlotSelect.append(
                            '<option disabled selected>No slots available</option>');
                        $('#timeSlotSelect').select2({
                            allowClear: false
                        });
                        $('#timeSlotSelect').next('.select2-container').addClass('no-slots');
                    }
                    $timeSlotSelect.trigger('change.select2');
                },
                error: function(xhr) {
                    console.error('Error fetching time slots:', xhr);
                }
            });
        }
        $(document).ready(function() {
            const slug = "{{ $restaurant->slug }}";

            $('#timeSlotSelect').select2({
                placeholder: "Select a time slot",
                allowClear: true,
                width: '100%'
            });


            flatpickr("#datepicker", {
                dateFormat: "Y-m-d",
                defaultDate: "today",
                minDate: "today",
                allowInput: true,
                wrap: false,
                onReady: function(selectedDates, dateStr) {
                    fetchTimeSlots(dateStr);
                }
            });

            $('#datepicker').on('change', function() {
                const selectedDate = $(this).val();
                fetchTimeSlots(selectedDate);
            });
        });
    </script>
    <script>
        const reviewCard = document.querySelector('.review-card');
        const dateSelect = document.querySelector('.date-select');
        const editbutton = document.querySelector('.edit-button');

        editbutton.addEventListener('click', function() {
            dateSelect.classList.remove('hidden');
            editbutton.classList.add('hidden');

            // Optional: prevent scroll logic from re-hiding it
            reviewCard.classList.add('editing');
        });


        document.addEventListener("DOMContentLoaded", function() {
            const navLinks = document.querySelectorAll(".review-link .nav-link");

            navLinks.forEach(link => {
                link.addEventListener("click", function() {
                    navLinks.forEach(l => l.classList.remove("active"));

                    this.classList.add("active");
                });
            });
        });
        const dateInput = document.getElementById("datepicker");
    </script>

    <script>
        function setEqualSlideHeights(className) {
            let maxHeight = 0;
            $(className + ' .slick-slide').css('height', 'auto');
            $(className + ' .slick-slide').each(function() {
                const h = $(this).outerHeight();
                if (h > maxHeight) maxHeight = h;
            });
            $(className + ' .slick-slide').css('height', maxHeight + 'px');
        }

        $('.restaurant-slider').on('setPosition', function() {
            setEqualSlideHeights('.restaurant-slider');
        });

        $('.restaurant-slider').slick({
            dots: false,
            infinite: false,
            speed: 300,
            slidesToShow: 2,
            slidesToScroll: 1,
            arrows: true,
            autoplay: true,
            autoplaySpeed: 2000,
            infinite: false,
            prevArrow: "<button type='button' class='slick-prev custome-arrow pull-left'><svg viewBox='0 0 24 24' class='w-8 h-8' xmlns='http://www.w3.org/2000/svg'><path d='M12.303 6.353a.5.5 0 0 1 .707 0l.707.708a.5.5 0 0 1 0 .707l-3.889 3.889 3.89 3.889a.5.5 0 0 1 0 .707l-.708.707a.5.5 0 0 1-.707 0l-4.95-4.95a.5.5 0 0 1 0-.707l4.95-4.95Z' fill='#2D333F'></path></svg></button>",
            nextArrow: "<button type='button' class='slick-next custome-arrow pull-right'><svg viewBox='0 0 24 24' class='w-8 h-8' xmlns='http://www.w3.org/2000/svg'><path d='m11.01 6.353 4.95 4.95a.5.5 0 0 1 0 .707l-4.95 4.95a.5.5 0 0 1-.707 0l-.707-.707a.5.5 0 0 1 0-.707l3.89-3.89-3.89-3.888a.5.5 0 0 1 0-.707l.707-.708a.5.5 0 0 1 .707 0Z' fill='#2D333F'></path></svg></button>",

            responsive: [{
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        slidesToShow: 1.2,
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('photoModal');
            const closeBtn = document.getElementById('closeModalBtn');
            const openButtons = document.querySelectorAll('[data-open-photo-modal]');

            openButtons.forEach(button => {
                button.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            closeBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {


            const dateInput = document.getElementById("datepicker");
            const timeSelect = document.getElementById("timeSlotSelect");
            const numberOfPersons = document.getElementById("personSelect");

            const customerName = document.getElementById("customerName");
            const customerEmail = document.getElementById("customerEmail");
            const customerPhone = document.getElementById("customerPhone");
            const bookingDetails = document.getElementById("bookingDetails");
        });
    </script>

    <script>
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
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: (data) => {
                    if (data.status === 'success') {
                        toastr.success(data.message || 'Thank you! Your reservation is confirmed.');
                        bookingForm[0].reset(); // Optionally reset form
                        $('#timeSlotSelect').val(null).trigger('change');
                        const selectedDate = $('#datepicker').val();
                        fetchTimeSlots(selectedDate);
                    } else {
                        toastr.error(data.message || 'Something went wrong. Please try again.');
                        bookingForm[0].reset();
                    }
                },
                error: (xhr) => {
                    // Try to get JSON error message
                    let message = 'Reservation failed. Please try again.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    toastr.error(message);
                }
            });
        });
    </script>
</body>

</html>