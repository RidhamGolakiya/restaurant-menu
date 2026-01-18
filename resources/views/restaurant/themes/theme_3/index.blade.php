@extends('restaurant.themes.theme_3.layout')

@section('title', $restaurant->name . ' | Home')

@section('content')
    <!-- Best Seller Section -->
    <section style="padding: 2.5rem 0;">
        <h2 class="section-title gs-reveal">Best Seller</h2>
        <div class="best-seller-grid">
            <a href="{{ route('restaurant.best-seller.food', $restaurant->slug) }}" class="bs-card gs-reveal">
                <div class="bs-card-bg"></div>
                <div class="bs-card-overlay"></div>
                <div class="bs-content">
                    <div class="bs-inner">
                        <h3 class="bs-title">Food Menu</h3>
                        <p class="bs-subtitle">View Collection</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('restaurant.best-seller.drink', $restaurant->slug) }}" class="bs-card gs-reveal">
                <div class="bs-card-bg"></div>
                <div class="bs-card-overlay"></div>
                <div class="bs-content">
                    <div class="bs-inner">
                        <h3 class="bs-title">Drink Menu</h3>
                        <p class="bs-subtitle">View Collection</p>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <!-- All Categories -->
    <section style="padding: 2.5rem 0;">
        <h2 class="section-title gs-reveal">All Categories</h2>
        <div class="card-grid" id="food-categories">
            @foreach($categories as $category)
            <a href="{{ route('restaurant.category', ['slug' => $restaurant->slug, 'categorySlug' => $category->slug ?? Str::slug($category->name)]) }}" class="cat-card gs-reveal">
                <div class="cat-img-box">
                    <img src="{{ $category->getFirstMediaUrl('category_image') ?: 'https://placehold.co/400x300?text=' . urlencode($category->name) }}" 
                         class="cat-img" 
                         alt="{{ $category->name }}"
                         onerror="this.onerror=null;this.src='https://placehold.co/400x300?text={{ urlencode($category->name) }}';">
                </div>
                <div class="cat-title">{{ $category->name }}</div>
            </a>
            @endforeach
        </div>
    </section>
@endsection

@push('styles')
<style>
    /* Home Specific Styles */
    .best-seller-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media (min-width: 768px) {
        .best-seller-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .bs-card {
        position: relative;
        height: 16rem;
        border-radius: 1.5rem;
        overflow: hidden;
        display: block;
        cursor: pointer;
    }
    @media (min-width: 768px) { .bs-card { height: 20rem; } }

    .bs-card-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #1f2937, #000);
        transition: transform 0.7s;
    }

    .bs-card:hover .bs-card-bg {
        transform: scale(1.05);
    }

    .bs-card-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.3);
        transition: background 0.3s;
    }
    .bs-card:hover .bs-card-overlay {
        background: rgba(0,0,0,0.1);
    }

    .bs-content {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 10;
        text-align: center;
        color: white;
    }

    .bs-inner {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(12px);
        padding: 1.5rem;
        border-radius: 1rem;
        width: 80%;
        max-width: 20rem;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    }
    .dark .bs-inner { background: rgba(0,0,0,0.3); }

    .bs-title {
        font-family: var(--font-serif);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }

    .bs-subtitle {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: 0.9;
    }

    /* Category Card */
    .cat-card {
        position: relative;
        height: 12rem;
        border-radius: 1.5rem;
        overflow: hidden;
        background-color: var(--card-bg, rgba(255, 255, 255, 0.9));
        border: 2px solid transparent;
        transition: all 0.3s ease;
        display: block;
    }
    .dark .cat-card {
        background-color: var(--card-bg, rgba(30, 41, 59, 0.6));
    }
    .cat-card:hover {
        transform: translateY(-4px);
        border-color: var(--hover-border-color, rgba(0, 0, 0, 0.15));
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .dark .cat-card:hover {
        border-color: var(--hover-border-color, rgba(255, 255, 255, 0.6));
    }

    .cat-img-box {
        height: 8rem;
        width: 100%;
        overflow: hidden;
        background-color: #e5e7eb;
        position: relative;
    }
    .dark .cat-img-box { background-color: rgba(255,255,255,0.05); }

    .cat-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .cat-card:hover .cat-img { transform: scale(1.05); }

    .cat-title {
        height: 4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-serif);
        font-weight: 700;
        font-size: 1.125rem;
        color: var(--text-primary, #0A0A0A);
    }
    .dark .cat-title {
        color: var(--text-primary, #FFFFFF);
    }
</style>
@endpush
