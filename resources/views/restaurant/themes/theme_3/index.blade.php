@extends('restaurant.themes.theme_3.layout')

@section('title', $restaurant->name . ' | Home')

@section('content')
    <!-- Best Seller Section -->
    <section>
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

    <!-- Base Categories Sections -->
    @foreach($baseCategories as $baseCategory)
    <section style="padding: 2.5rem 0;">
        <h2 class="section-title gs-reveal">{{ $baseCategory->name }}</h2>
        <div class="card-grid">
            @foreach($baseCategory->menuCategories as $category)
            <a href="{{ route('restaurant.category', ['slug' => $restaurant->slug, 'categorySlug' => $category->slug ?? Str::slug($category->name)]) }}" class="cat-card gs-reveal">
                <div class="cat-img-box">
                    @php
                        $categoryImage = $category->getFirstMediaUrl('category_image');
                    @endphp
                    <img src="{{ $categoryImage ?: 'https://placehold.co/400x300?text=' . urlencode($category->name) }}" 
                         class="cat-img" 
                         alt="{{ $category->name }}"
                         onerror="this.onerror=null;this.src='https://placehold.co/400x300?text={{ urlencode($category->name) }}';">
                </div>
                <div class="cat-title">{{ $category->name }}</div>
            </a>
            @endforeach
        </div>
    </section>
    @endforeach

    <!-- Gallery Section -->
    @if($restaurant->getMedia('photos')->count() > 0)
    <section>
        <h2 class="section-title gs-reveal">Gallery</h2>
        <div class="card-grid">
            @foreach($restaurant->getMedia('photos') as $index => $photo)
            <div class="cat-card gs-reveal" style="cursor: pointer;" onclick="openGallery({{ $loop->index }})">
                <div class="cat-img-box" style="height: 16rem;">
                    <img src="{{ $photo->getUrl() }}" 
                         class="cat-img" 
                         alt="Gallery Image"
                         style="transform: none;">
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Gallery Modal -->
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
    @endif
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
@endpush

@push('scripts')
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
@endpush
