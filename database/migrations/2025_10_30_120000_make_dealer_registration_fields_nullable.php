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
            // Make fields nullable that are optional in the API
            $table->string('pan_number')->nullable()->change();
            $table->string('business_city')->nullable()->change();
            $table->string('business_state')->nullable()->change();
            $table->string('business_pincode')->nullable()->change();
            $table->string('business_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealer_registrations', function (Blueprint $table) {
            // Revert to NOT NULL (note: this might fail if there are NULL values)
            $table->string('pan_number')->nullable(false)->change();
            $table->string('business_city')->nullable(false)->change();
            $table->string('business_state')->nullable(false)->change();
            $table->string('business_pincode')->nullable(false)->change();
            $table->string('business_type')->nullable(false)->change();
        });
    }
};

