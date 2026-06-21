<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TechNova — Premium Gadgets, Delivered Across Bangladesh')</title>
    <meta name="description" content="@yield('meta_description', 'TechNova brings premium, authentic gadgets to your doorstep anywhere in Bangladesh — Cash on Delivery, fast shipping, trusted quality.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans text-slateblack bg-white antialiased">

    {{-- Top utility bar — BD trust signals, always visible --}}
    <div class="bg-navy text-white text-xs sm:text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2 flex items-center justify-center sm:justify-between gap-2 flex-wrap">
            <p class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6c0 4.5 6 10 6 10s6-5.5 6-10a6 6 0 00-6-6z"/></svg>
                Cash on Delivery available nationwide
            </p>
            <p class="hidden sm:flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h7a1 1 0 011 1v3h2.4a1 1 0 01.9.55l1.6 3.2a1 1 0 01.1.45V14a1 1 0 01-1 1h-1a2 2 0 11-4 0H8a2 2 0 11-3.96.2A1 1 0 014 14V4z"/></svg>
                Fast Shipping across Bangladesh
            </p>
        </div>
    </div>

    {{-- Header / Navbar --}}
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-100">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/logo.svg') }}" alt="TechNova" class="h-9 w-auto">
            </a>

            <div class="hidden md:flex items-center gap-8 font-medium text-sm">
                <a href="{{ route('home') }}" class="text-slateblack/80 hover:text-navy transition-colors">Home</a>
                <a href="{{ route('products.index') }}" class="text-slateblack/80 hover:text-navy transition-colors">Shop</a>
                <a href="{{ route('products.index') }}#categories" class="text-slateblack/80 hover:text-navy transition-colors">Categories</a>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('products.index') }}" method="GET" class="hidden lg:flex items-center">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search gadgets..."
                           class="w-56 text-sm px-4 py-2.5 rounded-full border border-slate-200 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition">
                </form>

                <a href="{{ route('cart.index') }}" class="relative flex items-center justify-center w-10 h-10 rounded-full hover:bg-slate-100 transition-colors" aria-label="Cart">
                    <svg class="w-5 h-5 text-slateblack" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3.6-7.5H5.4M7 13L5.4 5.5M7 13l-1.6 3.2A1 1 0 006.3 18H17m-9 3.5a1 1 0 100-2 1 1 0 000 2zm9 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
                    <span id="cart-count-badge" class="absolute -top-1 -right-1 bg-accent text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center {{ session('cart_count', 0) > 0 ? '' : 'hidden' }}">
                        {{ session('cart_count', 0) }}
                    </span>
                </a>
            </div>
        </nav>
    </header>

    {{-- Flash messages --}}
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slateblack text-white mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <img src="{{ asset('images/logo.svg') }}" alt="TechNova" class="h-8 w-auto mb-4 brightness-0 invert">
                <p class="text-slate-400 text-sm leading-relaxed">Premium gadgets, sourced and delivered with trust — anywhere in Bangladesh.</p>
            </div>
            <div>
                <h4 class="font-display font-semibold text-sm uppercase tracking-wider text-slate-300 mb-4">Shop</h4>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li><a href="{{ route('products.index') }}" class="hover:text-accent transition-colors">All Products</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-accent transition-colors">New Arrivals</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-semibold text-sm uppercase tracking-wider text-slate-300 mb-4">Support</h4>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li>Cash on Delivery</li>
                    <li>Fast Shipping (BD-wide)</li>
                    <li>7-Day Replacement</li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-semibold text-sm uppercase tracking-wider text-slate-300 mb-4">Contact</h4>
                <p class="text-sm text-slate-400">Dhaka, Bangladesh</p>
                <p class="text-sm text-slate-400 mt-1">support@technova.com.bd</p>
            </div>
        </div>
        <div class="border-t border-white/10 py-5 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} TechNova. All rights reserved.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
