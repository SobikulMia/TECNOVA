<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Smartphones',
            'Smartwatches',
            'Wireless Earbuds',
            'Power Banks',
            'Laptop Accessories',
            'Smart Home Gadgets',
            'Gaming Accessories',
            'Cables & Chargers',
        ];

        foreach ($categories as $index => $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }
}
