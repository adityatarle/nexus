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
            $table->foreignId('agriculture_subcategory_id')->nullable()->after('agriculture_category_id')->constrained('agriculture_subcategories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agriculture_products', function (Blueprint $table) {
            $table->dropForeign(['agriculture_subcategory_id']);
            $table->dropColumn('agriculture_subcategory_id');
        });
    }
};
