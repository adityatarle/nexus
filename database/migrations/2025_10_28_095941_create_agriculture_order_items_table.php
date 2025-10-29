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
        Schema::create('agriculture_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agriculture_order_id')->constrained('agriculture_orders')->onDelete('cascade');
            $table->foreignId('agriculture_product_id')->constrained('agriculture_products')->onDelete('cascade');
            $table->string('product_name');
            $table->string('product_sku');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agriculture_order_items');
    }
};