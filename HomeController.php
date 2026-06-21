<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(protected ProductRepositoryInterface $productRepository)
    {
    }

    public function index(): View
    {
        $featuredProducts = $this->productRepository->featured(8);
        $categories = Category::active()->ordered()->get();

        return view('home.index', compact('featuredProducts', 'categories'));
    }
}
