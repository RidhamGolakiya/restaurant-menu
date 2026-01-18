@extends('restaurant.themes.theme_3.layout')

@section('title', isset($category) ? $category->name . ' | ' . $restaurant->name : ($title ?? 'Category'))

@section('content')
    <button class="back-btn" onclick="history.back()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.25rem;height:1.25rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        <span>Back</span>
    </button>

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
        <a href="{{ route('restaurant.product', ['slug' => $restaurant->slug, 'productSlug' => $product->slug ?? Str::slug($product->name)]) }}" class="product-card gs-reveal">
            @if($product->is_best_seller)
            <div class="pc-badge">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:1.5rem;height:1.5rem;"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" /></svg>
            </div>
            @endif
            <div class="pc-img-box">
                <img src="{{ $product->getFirstMediaUrl('menu_image') ?: 'https://placehold.co/400x300?text=' . urlencode($product->name) }}" 
                     alt="{{ $product->name }}" 
                     class="pc-img"
                     onerror="this.onerror=null;this.src='https://placehold.co/400x300?text={{ urlencode($product->name) }}';">
            </div>
            <div class="pc-content">
                <h3 class="pc-title">{{ $product->name }}</h3>
                <p class="pc-desc">{{ $product->description ?? $product->ingredients }}</p>
                <div class="pc-footer"><span class="pc-price">{{ $restaurant->currency->icon ?? '₹' }}{{ $product->price }}</span><span class="pc-note">No extra GST</span></div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div id="no-items" style="text-align:center; padding: 5rem 0; color: var(--text-secondary);">
        <p style="font-size: 1.25rem; margin-bottom: 0.5rem;">No items found.</p>
        <p style="font-size: 0.875rem; opacity: 0.7;">Try browsing other categories.</p>
    </div>
    @endif
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
</style>
@endpush
