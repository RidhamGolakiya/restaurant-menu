@extends('restaurant.themes.theme_3.layout')

@section('title', isset($category) ? $category->name . ' | ' . $restaurant->name : ($title ?? 'Category'))

@section('content')
    <button class="back-btn" onclick="history.back()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.25rem;height:1.25rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        <span>Back</span>
    </button>

    <div x-data="{ productModalOpen: false, activeProduct: null }">
        <div style="display:flex; align-items:center; margin-bottom: 2rem;">
            <h1 class="section-title" id="cat-title" style="margin-bottom:0;">
                {{ isset($category) ? $category->name : ($title ?? 'Category') }}
            </h1>
            <span id="item-count" style="margin-left: 1rem; font-size: 0.875rem; color: var(--text-secondary); background: var(--card-bg); padding: 0.25rem 0.75rem; border-radius: 9999px; border: 1px solid var(--border-color);">
                {{ $products->count() }} Items
            </span>
        </div>

        @if($products->count() > 0)
        <div class="product-grid" id="product-container">
            @foreach($products as $product)
            <div @click="activeProduct = {{ $product->toJson() }}; productModalOpen = true" class="product-card gs-reveal" style="cursor: pointer;">
                @if($product->is_best_seller)
                <div class="pc-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:1.5rem;height:1.5rem;"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" /></svg>
                </div>
                @endif
                <div class="pc-img-box">
                    @php
                       $productImage = $product->getFirstMediaUrl('menu_image');
                    @endphp
                    <img src="{{ $productImage ?: 'https://placehold.co/400x300?text=' . urlencode($product->name) }}" 
                         alt="{{ $product->name }}" 
                         class="pc-img"
                         onerror="this.onerror=null;this.src='https://placehold.co/400x300?text={{ urlencode($product->name) }}';">
                </div>
                <div class="pc-content">
                    <h3 class="pc-title">{{ $product->name }}</h3>
                    <p class="pc-desc">{{ $product->description ?? $product->ingredients }}</p>
                    <div class="pc-footer"><span class="pc-price">{{ $restaurant->currency->icon ?? '₹' }}{{ $product->price }}</span><span class="pc-note">No extra GST</span></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div id="no-items" style="text-align:center; padding: 5rem 0; color: var(--text-secondary);">
            <p style="font-size: 1.25rem; margin-bottom: 0.5rem;">No items found.</p>
            <p style="font-size: 0.875rem; opacity: 0.7;">Try browsing other categories.</p>
        </div>
        @endif

        <!-- Modal -->
        <div x-show="productModalOpen" style="display: none; position: fixed; inset: 0; z-index: 50;" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="productModalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="productModalOpen = false"
                 style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);"></div>

            <!-- Modal Content -->
            <div class="modal-content-wrapper" style="pointer-events: none; position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem;">
                <div x-show="productModalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
                     @click.stop
                     class="modal-card">
                    
                    <div class="modal-grid">
                        <div class="modal-img-container">
                            <!-- Image handling via Alpine -->
                            <template x-if="activeProduct?.media && activeProduct.media.length > 0">
                                <img :src="activeProduct?.media[0]?.original_url" alt="Product Image" class="modal-img">
                            </template>
                             <template x-if="!activeProduct?.media || activeProduct.media.length === 0">
                                <div class="modal-placeholder">
                                    <svg style="width: 4rem; height: 4rem; color: #cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </template>
                        </div>
                        <div class="modal-details">
                            <button @click="productModalOpen = false" class="close-btn">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>

                            <span class="modal-category" x-text="activeProduct?.category?.name || '{{ isset($category) ? $category->name : 'Menu Item' }}'"></span>
                            <h3 class="modal-title" x-text="activeProduct?.name"></h3>
                            <div class="modal-price">
                                {{ $restaurant->currency->icon ?? '₹' }}<span x-text="activeProduct?.price"></span>
                            </div>
                            
                            <div class="modal-desc-scroll">
                                <p class="modal-desc" x-text="activeProduct?.description || activeProduct?.ingredients || 'No description available.'"></p>
                                
                                <div class="modal-divider"></div>

                                <template x-if="activeProduct?.ingredients">
                                    <div>
                                        <h4 class="modal-ingredients-title">Ingredients</h4>
                                        <p class="modal-ingredients-text" x-text="activeProduct.ingredients"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .product-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media (min-width: 640px) { .product-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 768px) { .product-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1024px) { .product-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (min-width: 1280px) { .product-grid { grid-template-columns: repeat(5, 1fr); } }

    .product-card {
        background-color: var(--card-bg, rgba(255, 255, 255, 0.9));
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid transparent;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .dark .product-card {
        background-color: var(--card-bg, rgba(30, 41, 59, 0.6));
    }

    .product-card:hover {
        transform: translateY(-4px) scale(1.02);
        border-color: var(--hover-border-color, rgba(0, 0, 0, 0.15));
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .dark .product-card:hover {
        border-color: var(--hover-border-color, rgba(255, 255, 255, 0.6));
    }

    .pc-badge {
        position: absolute;
        top: 0.75rem;
        left: 0.75rem;
        z-index: 10;
        color: #eab308;
        filter: drop-shadow(0 4px 3px rgb(0 0 0 / 0.07));
    }

    .pc-img-box {
        height: 12rem;
        background-color: #f3f4f6;
        overflow: hidden;
    }
    .dark .pc-img-box { background-color: #1e293b; }

    .pc-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s;
    }
    .product-card:hover .pc-img { transform: scale(1.05); }

    .pc-content {
        padding: 1rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .pc-title {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pc-desc {
        font-size: 0.75rem;
        color: var(--text-secondary, #334155);
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
    }
    .dark .pc-desc {
        color: var(--text-secondary, #D1D5DB);
    }

    .pc-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .pc-price {
        font-size: 1.125rem;
        font-weight: 700;
    }

    .pc-note {
        font-size: 0.625rem;
        color: var(--text-secondary, #334155);
        font-weight: 500;
    }
    .dark .pc-note {
        color: var(--text-secondary, #D1D5DB);
    }

    /* Modal Styles */
    .modal-card {
        pointer-events: auto;
        width: 100%;
        max-width: 48rem; /* Reduced width */
        background-color: #ffffff;
        border-radius: 1rem; /* Slightly smaller radius */
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    @media (min-width: 768px) {
        .modal-card { flex-direction: row; height: 26rem; } /* Reduced height */
    }
    .dark .modal-card {
        background-color: #1e293b;
    }

    .close-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 20;
        background: transparent;
        border: none; /* Thinner border */
        border-radius: 9999px;
        width: 1.5rem; /* Smaller button */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: #334155;
    }
    .close-btn:hover { color: #000; border-color: #000; background: rgba(0,0,0,0.05); }
    .dark .close-btn { color: #94a3b8; border-color: #94a3b8; }
    .dark .close-btn:hover { color: #fff; border-color: #fff; background: rgba(255,255,255,0.05); }

    .modal-grid {
        display: contents; /* Use flex from container */
    }

    .modal-img-container {
        position: relative;
        width: 100%;
        height: 16rem;
        background-color: #f8fafc; /* Very light gray/white for image area? Screenshot looks like white or placeholder gray */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media (min-width: 768px) {
        .modal-img-container { width: 50%; height: 100%; }
    }
    .dark .modal-img-container { background-color: #0f172a; }

    .modal-img {
        width: 60%; /* Smaller image centered like screenshot placeholder? Or cover? */
        height: auto;
        max-height: 80%;
        object-fit: contain; /* Screenshot shows placeholder icon centered. Real food images usually cover. Let's stick to cover for real images? User screenshot has 'Chicken Pizza' so presumably it should be a Photo. */
    }
    /* If real image, cover */
    .modal-img[src*="http"] { width: 100%; height: 100%; object-fit: cover; }

    .modal-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f1f5f9;
        color: #cbd5e1;
    }

    .modal-details {
        padding: 3rem;
        width: 100%;
        background-color: #e6e5e5; /* Match screenshot light gray background */
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
    }
    @media (min-width: 768px) {
        .modal-details { width: 50%; }
    }
    .dark .modal-details { background-color: #1e293b; }

    .modal-category {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #ca8a04; /* Amber 600 - darker gold */
        margin-bottom: 0.5rem;
    }

    .modal-title {
        font-family: var(--font-serif);
        font-size: 2.5rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 0.5rem;
        line-height: 1.1;
    }
    .dark .modal-title { color: #ffffff; }

    .modal-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #eab308; /* Amber 500 */
        margin-bottom: 1.5rem;
    }

    .modal-desc-scroll {
        overflow-y: auto;
        /* Custom scrollbar if needed */
    }
    
    .modal-desc {
        font-size: 1rem;
        line-height: 1.6;
        color: #4b5563;
        margin-bottom: 1.5rem;
    }
    .dark .modal-desc { color: #d1d5db; }

    .modal-divider {
        height: 1px;
        background-color: #d1d5db;
        margin-bottom: 1.5rem;
        width: 100%;
    }
    .dark .modal-divider { background-color: #334155; }

    .modal-ingredients-title {
        font-weight: 700;
        font-size: 0.875rem;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    .dark .modal-ingredients-title { color: #f3f4f6; }

    .modal-ingredients-text {
        font-size: 0.875rem;
        color: #4b5563;
        line-height: 1.5;
    }
    .dark .modal-ingredients-text { color: #9ca3af; }
</style>
@endpush
