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
        Schema::table('agriculture_order_items', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->nullable()->after('price');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('original_price');
            $table->foreignId('offer_id')->nullable()->after('discount_amount')->constrained('offers')->onDelete('set null');
            $table->json('offer_details')->nullable()->after('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agriculture_order_items', function (Blueprint $table) {
            $table->dropForeign(['offer_id']);
            $table->dropColumn(['original_price', 'discount_amount', 'offer_id', 'offer_details']);
        });
    }
};
