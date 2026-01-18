@extends('restaurant.themes.theme_3.layout')

@section('title', $product->name . ' | ' . $restaurant->name)

@section('content')
    <button class="back-btn" onclick="history.back()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.25rem;height:1.25rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        <span>Back</span>
    </button>

    <div id="product-area" class="details-grid gs-reveal">
        <div class="img-container">
            @if($product->is_best_seller || $product->is_best_food || $product->is_best_drink)
            <div class="bs-tag">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:1rem;height:1rem;"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" /></svg> 
                Best Seller
            </div>
            @endif
            <img src="{{ $product->getFirstMediaUrl('menu_image') ?: 'https://placehold.co/600x600?text=' . urlencode($product->name) }}" 
                 alt="{{ $product->name }}" 
                 class="prod-img"
                 onerror="this.onerror=null;this.src='https://placehold.co/600x600?text={{ urlencode($product->name) }}';">
        </div>
        <div class="info-container">
            <div class="cats">
                @if($product->category)
                <span class="main-cat">{{ $product->category->name }}</span>
                @endif
                @if($product->dietary_type)
                <span class="sub-cat">• {{ ucfirst(str_replace('_', ' ', $product->dietary_type)) }}</span>
                @endif
            </div>
            <h1 class="prod-title">{{ $product->name }}</h1>
            <p class="prod-desc">{{ $product->description ?? $product->ingredients }}</p>
            
            <div class="purchase-bar">
                <div class="price-group">
                    <span class="final-price">{{ $restaurant->currency->icon ?? '₹' }}{{ $product->price }}</span>
                    <span class="price-note">Inclusive of all taxes</span>
                </div>
                <button class="add-btn">Add to Order</button>
            </div>
            
            <div class="features">
                <div class="feat-card">
                    <h4 class="feat-title">Fresh Ingredients</h4>
                    <p class="feat-text">Sourced locally every morning.</p>
                </div>
                <div class="feat-card">
                    <h4 class="feat-title">Chef's Special</h4>
                    <p class="feat-text">Prepared with authentic recipes.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .details-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }
    @media (min-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }
    }

    .img-container {
        border-radius: 1.5rem;
        overflow: hidden;
        background-color: #f3f4f6;
        aspect-ratio: 4/3;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        position: relative;
    }
    @media (min-width: 768px) { .img-container { aspect-ratio: 1/1; } }
    .dark .img-container { background-color: #1e293b; }

    .prod-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bs-tag {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(8px);
        color: #facc15;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .info-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .cats {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .main-cat {
        background-color: var(--text-primary);
        color: var(--background);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .sub-cat {
        color: var(--text-secondary);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .prod-title {
        font-family: var(--font-serif);
        font-size: 2.25rem;
        font-weight: 700;
        line-height: 1.1;
        margin-bottom: 1rem;
        color: var(--text-primary);
    }
    @media (min-width: 768px) { .prod-title { font-size: 3rem; } }

    .prod-desc {
        font-size: 1.125rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .purchase-bar {
        padding: 1.5rem 0;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .price-group {
        display: flex;
        flex-direction: column;
    }
    .final-price {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    .price-note {
        font-size: 0.75rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .add-btn {
        background-color: var(--text-primary);
        color: var(--background);
        padding: 0.75rem 2rem;
        border-radius: 9999px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        transition: opacity 0.2s;
    }
    .add-btn:hover { opacity: 0.9; }

    .features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .feat-card {
        padding: 1rem;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
    }
    .feat-title {
        font-weight: 700;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    .feat-text {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }
</style>
@endpush
