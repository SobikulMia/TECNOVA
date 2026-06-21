@extends('layouts.app')

@section('title', 'Order Confirmed — TechNova')

@section('content')
<section class="max-w-3xl mx-auto px-4 sm:px-6 py-16 sm:py-24 text-center">
    <div class="w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>

    <h1 class="font-display text-3xl sm:text-4xl font-bold text-slateblack">Thank you, {{ $order->customer_name }}!</h1>
    <p class="text-slate-500 mt-3">Your order has been placed successfully. We'll call you shortly to confirm delivery.</p>

    <div class="mt-10 bg-slate-50 rounded-xl2 p-6 sm:p-8 text-left">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-4">
            <div>
                <p class="text-xs text-slate-400">Order Number</p>
                <p class="font-display font-bold text-slateblack">{{ $order->order_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400">Total Amount</p>
                <p class="font-display font-bold text-navy text-lg">৳{{ number_format($order->total_amount) }}</p>
            </div>
        </div>

        <div class="space-y-3 mb-4">
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">{{ $item->product_name }} <span class="text-slate-400">× {{ $item->quantity }}</span></span>
                    <span class="font-medium text-slateblack">৳{{ number_format($item->line_total) }}</span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-slate-200 pt-4 space-y-1.5 text-sm">
            <div class="flex justify-between text-slate-500">
                <span>Subtotal</span><span>৳{{ number_format($order->subtotal) }}</span>
            </div>
            <div class="flex justify-between text-slate-500">
                <span>Delivery Charge</span><span>৳{{ number_format($order->delivery_charge) }}</span>
            </div>
        </div>

        <div class="border-t border-slate-200 mt-4 pt-4 text-sm grid sm:grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-slate-400">Delivery Address</p>
                <p class="text-slateblack font-medium">{{ $order->delivery_address }}, {{ $order->district }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Payment Method</p>
                <p class="text-slateblack font-medium uppercase">{{ $order->payment_method }} — {{ ucfirst($order->payment_status) }}</p>
            </div>
        </div>
    </div>

    <a href="{{ route('products.index') }}" class="btn-outline mt-10 inline-flex">Continue Shopping</a>
</section>
@endsection
