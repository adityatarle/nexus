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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            
            // Offer Type: 'product', 'category', 'subcategory', 'general'
            $table->enum('offer_type', ['product', 'category', 'subcategory', 'general'])->default('general');
            
            // Related IDs (nullable - for general offers)
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            
            // Discount Type: 'percentage', 'fixed'
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // Percentage or fixed amount
            
            // Minimum purchase requirements
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->integer('min_quantity')->nullable();
            
            // Validity
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            
            // Usage limits
            $table->integer('max_uses')->nullable(); // Total uses allowed
            $table->integer('max_uses_per_user')->nullable(); // Per user limit
            $table->integer('used_count')->default(0);
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            
            // Priority (higher priority offers apply first)
            $table->integer('priority')->default(0);
            
            // Terms and conditions
            $table->text('terms_conditions')->nullable();
            
            // Applicable to user types
            $table->boolean('for_customers')->default(true);
            $table->boolean('for_dealers')->default(false);
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('product_id')->references('id')->on('agriculture_products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('agriculture_categories')->onDelete('cascade');
            $table->foreign('subcategory_id')->references('id')->on('agriculture_subcategories')->onDelete('cascade');
            
            // Indexes
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index(['offer_type', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
