@extends('layouts.app')

@section('title', 'Your Cart — TechNova')

@section('content')
<section class="max-w-5xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
    <h1 class="font-display text-3xl font-bold text-slateblack mb-10">Your Cart</h1>

    @if($items->isEmpty())
        <div class="text-center py-24 bg-slate-50 rounded-xl2">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3.6-7.5H5.4M7 13L5.4 5.5"/></svg>
            <p class="text-slate-500 mb-6">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="btn-primary">Start Shopping</a>
        </div>
    @else
        <div id="cart-items-wrapper" class="space-y-4">
            @foreach($items as $line)
                <div class="flex items-center gap-4 bg-white border border-slate-100 rounded-xl2 p-4 sm:p-5" data-cart-line="{{ $line['product']->id }}">
                    <img src="{{ asset($line['product']->main_image) }}" alt="{{ $line['product']->name }}"
                         class="w-20 h-20 object-cover rounded-lg bg-slate-50 shrink-0"
                         onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">

                    <div class="flex-1 min-w-0">
                        <a href="{{ route('products.show', $line['product']) }}" class="font-medium text-sm text-slateblack hover:text-navy transition-colors line-clamp-1">
                            {{ $line['product']->name }}
                        </a>
                        <p class="text-navy font-display font-bold text-sm mt-1">৳{{ number_format($line['product']->final_price) }}</p>
                    </div>

                    <div class="flex items-center border border-slate-200 rounded-full">
                        <button type="button" class="cart-qty-btn w-9 h-9 text-slate-500 hover:text-navy" data-action="decrease" data-product-id="{{ $line['product']->id }}">−</button>
                        <span class="w-8 text-center text-sm font-semibold cart-qty-value">{{ $line['quantity'] }}</span>
                        <button type="button" class="cart-qty-btn w-9 h-9 text-slate-500 hover:text-navy" data-action="increase" data-product-id="{{ $line['product']->id }}">+</button>
                    </div>

                    <p class="w-20 text-right font-semibold text-sm shrink-0">৳{{ number_format($line['line_total']) }}</p>

                    <button type="button" class="cart-remove-btn text-slate-400 hover:text-red-500 shrink-0" data-product-id="{{ $line['product']->id }}" aria-label="Remove">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50 rounded-xl2 p-6">
            <div>
                <p class="text-sm text-slate-500">Subtotal</p>
                <p id="cart-subtotal" class="font-display text-2xl font-bold text-slateblack">৳{{ number_format($subtotal) }}</p>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn-primary">
                Proceed to Checkout
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    @endif
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    async function postJson(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            body: JSON.stringify(body),
        });
        return res.json();
    }

    document.querySelectorAll('.cart-qty-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const productId = parseInt(btn.dataset.productId, 10);
            const line = btn.closest('[data-cart-line]');
            const valueEl = line.querySelector('.cart-qty-value');
            let qty = parseInt(valueEl.textContent, 10);
            qty = btn.dataset.action === 'increase' ? qty + 1 : Math.max(0, qty - 1);

            const data = await postJson('{{ route('cart.update') }}', { product_id: productId, quantity: qty });

            if (data.success) {
                if (qty <= 0) {
                    line.remove();
                } else {
                    valueEl.textContent = qty;
                }
                document.getElementById('cart-subtotal').textContent = '৳' + Number(data.subtotal).toLocaleString();
                const badge = document.getElementById('cart-count-badge');
                if (badge) {
                    badge.textContent = data.cart_count;
                    badge.classList.toggle('hidden', data.cart_count <= 0);
                }
                if (document.querySelectorAll('[data-cart-line]').length === 0) {
                    location.reload();
                }
            }
        });
    });

    document.querySelectorAll('.cart-remove-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const productId = parseInt(btn.dataset.productId, 10);
            const data = await postJson('{{ route('cart.remove') }}', { product_id: productId });

            if (data.success) {
                btn.closest('[data-cart-line]').remove();
                document.getElementById('cart-subtotal').textContent = '৳' + Number(data.subtotal).toLocaleString();
                const badge = document.getElementById('cart-count-badge');
                if (badge) {
                    badge.textContent = data.cart_count;
                    badge.classList.toggle('hidden', data.cart_count <= 0);
                }
                if (document.querySelectorAll('[data-cart-line]').length === 0) {
                    location.reload();
                }
            }
        });
    });
});
</script>
@endpush
@endsection
