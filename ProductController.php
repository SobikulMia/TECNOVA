<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected ProductRepositoryInterface $productRepository)
    {
    }

    /**
     * Shop listing page — optionally filtered by category or search term.
     */
    public function index(Request $request): View
    {
        $categories = Category::active()->ordered()->get();

        $term = $request->query('q');
        $categorySlug = $request->query('category');
        $categoryId = $categorySlug
            ? Category::active()->where('slug', $categorySlug)->value('id')
            : null;

        $products = $term
            ? $this->productRepository->search($term, 12)
            : $this->productRepository->paginateActive(12, $categoryId);

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'activeCategorySlug' => $categorySlug,
            'searchTerm' => $term,
        ]);
    }

    /**
     * Single product detail page, resolved by slug via route model binding.
     */
    public function show(Product $product): View
    {
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
