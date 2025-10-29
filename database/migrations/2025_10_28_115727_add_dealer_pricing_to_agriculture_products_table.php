<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agriculture_products', function (Blueprint $table) {
            // Dealer pricing fields
            $table->decimal('dealer_price', 10, 2)->nullable()->after('sale_price');
            $table->decimal('dealer_sale_price', 10, 2)->nullable()->after('dealer_price');
            
            // Bulk pricing tiers (optional quantity-based discounts)
            $table->json('bulk_pricing_tiers')->nullable()->after('dealer_sale_price');
            
            // Minimum order quantity for dealers
            $table->integer('dealer_min_quantity')->default(1)->after('bulk_pricing_tiers');
            
            // Dealer-specific fields
            $table->boolean('is_dealer_exclusive')->default(false)->after('dealer_min_quantity');
            $table->text('dealer_notes')->nullable()->after('is_dealer_exclusive');
            
            // Indexes for performance
            $table->index(['is_dealer_exclusive', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agriculture_products', function (Blueprint $table) {
            $table->dropColumn([
                'dealer_price',
                'dealer_sale_price',
                'bulk_pricing_tiers',
                'dealer_min_quantity',
                'is_dealer_exclusive',
                'dealer_notes'
            ]);
        });
    }
};