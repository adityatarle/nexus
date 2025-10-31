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
            $table->string('primary_image')->nullable()->after('description');
            $table->text('gallery_images')->nullable()->after('primary_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agriculture_products', function (Blueprint $table) {
            $table->dropColumn(['primary_image', 'gallery_images']);
        });
    }
};




