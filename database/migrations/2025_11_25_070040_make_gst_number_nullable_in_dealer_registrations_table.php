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
        Schema::table('dealer_registrations', function (Blueprint $table) {
            // Drop the unique constraint on gst_number
            $table->dropUnique(['gst_number']);
            // Drop the index on gst_number
            $table->dropIndex(['gst_number']);
            // Make gst_number nullable
            $table->string('gst_number')->nullable()->change();
            // Add index back (allows nulls, but unique values must be unique)
            $table->index('gst_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealer_registrations', function (Blueprint $table) {
            // Drop the index
            $table->dropIndex(['gst_number']);
            // Make gst_number not nullable
            $table->string('gst_number')->nullable(false)->change();
            // Add unique constraint back
            $table->unique('gst_number');
            // Add index back
            $table->index('gst_number');
        });
    }
};
