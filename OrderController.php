<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Order confirmation / thank-you page, resolved by order_number via route model binding.
     */
    public function confirmation(Order $order): View
    {
        return view('orders.confirmation', compact('order'));
    }
}
