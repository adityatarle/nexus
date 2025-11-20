<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify payment_status ENUM to include 'not_required'
        DB::statement("ALTER TABLE `agriculture_orders` MODIFY COLUMN `payment_status` ENUM('pending', 'paid', 'failed', 'refunded', 'not_required') DEFAULT 'pending'");
        
        // Modify order_status ENUM to include 'inquiry'
        DB::statement("ALTER TABLE `agriculture_orders` MODIFY COLUMN `order_status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'inquiry') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert payment_status ENUM to original values
        DB::statement("ALTER TABLE `agriculture_orders` MODIFY COLUMN `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending'");
        
        // Revert order_status ENUM to original values
        DB::statement("ALTER TABLE `agriculture_orders` MODIFY COLUMN `order_status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
};
