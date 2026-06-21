@extends('layouts.app')

@section('title', $product->name . ' — TechNova')
@section('meta_description', $product->short_description ?? str(strip_tags($product->description ?? ''))->limit(155))

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-14">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-slate-500 mb-8 flex items-center gap-2 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-navy transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-navy transition-colors">Shop</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-navy transition-colors">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-slateblack font-medium">{{ $product->name }}</span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">

        {{-- ============ IMAGE GALLERY ============ --}}
        <div>
            @php $images = $product->images_json ?? []; @endphp
            <div class="aspect-square bg-slate-50 rounded-xl2 overflow-hidden mb-4 border border-slate-100">
                <img id="main-product-image"
                     src="{{ asset($product->main_image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover"
                     onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
            </div>

            @if(count($images) > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($images as $image)
                        <button type="button"
                                onclick="document.getElementById('main-product-image').src='{{ asset($image) }}'"
                                class="aspect-square bg-slate-50 rounded-lg overflow-hidden border-2 border-transparent hover:border-accent transition-colors">
                            <img src="{{ asset($image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover"
                                 onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============ PRODUCT INFO ============ --}}
        <div>
            <p class="text-accent font-semibold text-sm uppercase tracking-wider mb-2">{{ $product->category->name ?? 'Gadget' }}</p>
            <h1 class="font-display text-3xl sm:text-4xl font-bold text-slateblack leading-tight">{{ $product->name }}</h1>

            @if($product->short_description)
                <p class="text-slate-500 mt-3 leading-relaxed">{{ $product->short_description }}</p>
            @endif

            {{-- Price block --}}
            <div class="mt-6 flex items-center gap-3">
                <span class="font-display text-3xl font-bold text-navy">৳{{ number_format($product->final_price) }}</span>
                @if($product->has_discount)
                    <span class="text-lg text-slate-400 line-through">৳{{ number_format($product->regular_price) }}</span>
                    <span class="bg-accent-coral/10 text-accent-coral text-xs font-bold px-2.5 py-1 rounded-full">Save {{ $product->discount_percent }}%</span>
                @endif
            </div>

            {{-- Dynamic stock indicator --}}
            <div class="mt-5">
                @if($product->stock_status === 'in_stock')
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 bg-emerald-50 px-3.5 py-2 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> In Stock — Ready to Ship
                    </span>
                @elseif($product->stock_status === 'low_stock')
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 bg-amber-50 px-3.5 py-2 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Low Stock — Only a few left!
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 bg-red-50 px-3.5 py-2 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Out of Stock
                    </span>
                @endif
            </div>

            {{-- Add to cart --}}
            <div class="mt-8 flex items-center gap-4">
                <div class="flex items-center border border-slate-200 rounded-full">
                    <button type="button" onclick="document.getElementById('qty-input').stepDown()" class="w-11 h-11 flex items-center justify-center text-slate-500 hover:text-navy">−</button>
                    <input id="qty-input" type="number" value="1" min="1" max="20" class="w-12 text-center border-0 focus:ring-0 text-sm font-semibold">
                    <button type="button" onclick="document.getElementById('qty-input').stepUp()" class="w-11 h-11 flex items-center justify-center text-slate-500 hover:text-navy">+</button>
                </div>

                @if($product->stock_status !== 'out_of_stock')
                    <button type="button"
                            data-add-to-cart
                            data-product-id="{{ $product->id }}"
                            data-quantity-target="#qty-input"
                            class="btn-primary flex-1">
                        Add to Cart
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3.6-7.5H5.4M7 13L5.4 5.5"/></svg>
                    </button>
                @else
                    <button type="button" disabled class="flex-1 bg-slate-200 text-slate-400 font-semibold text-sm px-6 py-3.5 rounded-full cursor-not-allowed">
                        Currently Unavailable
                    </button>
                @endif
            </div>

            {{-- Trust row --}}
            <div class="mt-8 grid grid-cols-3 gap-3 border-t border-slate-100 pt-6">
                <div class="text-center">
                    <svg class="w-5 h-5 text-navy mx-auto mb-1.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-slate-500">Cash on Delivery</p>
                </div>
                <div class="text-center">
                    <svg class="w-5 h-5 text-navy mx-auto mb-1.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <p class="text-xs text-slate-500">Fast Shipping</p>
                </div>
                <div class="text-center">
                    <svg class="w-5 h-5 text-navy mx-auto mb-1.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-slate-500">100% Authentic</p>
                </div>
            </div>

            {{-- Specifications table --}}
            @if(!empty($product->specifications_json))
                <div class="mt-10">
                    <h3 class="font-display font-semibold text-lg text-slateblack mb-4">Specifications</h3>
                    <ul class="divide-y divide-slate-100 border border-slate-100 rounded-xl2 overflow-hidden">
                        @foreach($product->specifications_json as $key => $value)
                            <li class="flex items-start gap-3 px-5 py-3.5 text-sm odd:bg-slate-50/60">
                                <svg class="w-4 h-4 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span class="text-slate-500 w-32 sm:w-40 shrink-0 font-medium">{{ $key }}</span>
                                <span class="text-slateblack">{{ $value }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Description --}}
            @if($product->description)
                <div class="mt-10">
                    <h3 class="font-display font-semibold text-lg text-slateblack mb-3">Description</h3>
                    <div class="text-slate-600 leading-relaxed text-sm whitespace-pre-line">{{ $product->description }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============ RELATED PRODUCTS ============ --}}
    @if($relatedProducts->isNotEmpty())
        <div class="mt-24">
            <h2 class="font-display text-2xl font-bold text-slateblack mb-8">You May Also Like</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 sm:gap-7">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('products.show', $related) }}" class="product-card group block">
                        <div class="relative aspect-square bg-slate-50 overflow-hidden">
                            <img src="{{ asset($related->main_image) }}" alt="{{ $related->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                 onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium text-sm text-slateblack line-clamp-1 group-hover:text-navy transition-colors">{{ $related->name }}</h3>
                            <span class="font-display font-bold text-navy text-sm">৳{{ number_format($related->final_price) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
