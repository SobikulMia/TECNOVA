<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();

            $table->string('name', 180);
            $table->string('slug', 200)->unique();
            $table->string('short_description', 255)->nullable();
            $table->longText('description')->nullable();

            // Flexible spec sheet, e.g. {"RAM":"8GB","Storage":"256GB","Battery":"5000mAh"}
            $table->json('specifications_json')->nullable();

            // Pricing — buying_price stays internal/admin-only, never shown on storefront
            $table->decimal('regular_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('buying_price', 10, 2)->nullable();

            // Stock
            $table->enum('stock_status', ['in_stock', 'low_stock', 'out_of_stock'])->default('in_stock');
            $table->unsignedInteger('stock_quantity')->default(0);

            // Image paths/URLs as JSON array: ["products/abc1.jpg", "products/abc2.jpg"]
            $table->json('images_json')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            // Warehouse API readiness — nullable until 3rd-party sync is wired up
            $table->string('warehouse_product_id')->nullable()->index();
            $table->timestamp('warehouse_synced_at')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'stock_status']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
