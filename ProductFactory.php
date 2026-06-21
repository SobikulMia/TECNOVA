<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'AeroBuds Pro 2',
            'NovaWatch GT',
            'PowerCell 20000mAh',
            'FlexCharge 65W GaN Adapter',
            'SoundCore Mini Speaker',
            'UltraLite Laptop Stand',
            'PixelView Smartphone X12',
            'NightGlow RGB Mouse',
            'AirFlow Cooling Pad',
            'SnapGrip Phone Mount',
            'TurboCharge USB-C Cable',
            'EchoSense Smart Plug',
            'CrystalClear Screen Protector',
            'TrekPack Gadget Organizer',
            'PulseBand Fitness Tracker',
        ]).' '.$this->faker->numberBetween(1, 999);

        $regularPrice = $this->faker->randomElement([990, 1490, 1990, 2490, 3490, 4990, 6990, 9990]);
        $hasDiscount = $this->faker->boolean(35);

        return [
            'category_id' => Category::query()->inRandomOrder()->value('id'),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'short_description' => $this->faker->sentence(10),
            'description' => $this->faker->paragraphs(3, true),
            'specifications_json' => [
                'Brand' => $this->faker->randomElement(['TechNova Select', 'NovaTech Pro', 'Generic OEM']),
                'Warranty' => $this->faker->randomElement(['7-Day Replacement', '30-Day Warranty', '1-Year Warranty']),
                'Color' => $this->faker->randomElement(['Black', 'White', 'Midnight Blue', 'Space Gray']),
                'Connectivity' => $this->faker->randomElement(['Bluetooth 5.3', 'USB-C', 'Wi-Fi 6', 'Type-C Fast Charge']),
                'Battery Life' => $this->faker->randomElement(['Up to 8 hours', 'Up to 24 hours', 'Up to 30 days standby']),
            ],
            'regular_price' => $regularPrice,
            'sale_price' => $hasDiscount ? round($regularPrice * $this->faker->randomFloat(2, 0.65, 0.85)) : null,
            'buying_price' => round($regularPrice * 0.55),
            'stock_status' => $this->faker->randomElement(['in_stock', 'in_stock', 'in_stock', 'low_stock', 'out_of_stock']),
            'stock_quantity' => $this->faker->numberBetween(0, 150),
            'images_json' => ['images/placeholder-product.svg'],
            'is_featured' => $this->faker->boolean(30),
            'is_active' => true,
            'warehouse_product_id' => null,
        ];
    }
}
