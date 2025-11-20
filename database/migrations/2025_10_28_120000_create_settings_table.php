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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('type')->default('text'); // text, number, boolean, json, etc.
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('group');
            $table->index('key');
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'site_name',
                'value' => 'Nexus Agriculture',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Site Name',
                'description' => 'Name of your website',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_email',
                'value' => 'info@nexusagriculture.com',
                'group' => 'general',
                'type' => 'email',
                'label' => 'Site Email',
                'description' => 'Primary contact email',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_phone',
                'value' => '+1 234 567 8900',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Site Phone',
                'description' => 'Primary contact phone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_address',
                'value' => '123 Agriculture St, Farm City, FC 12345',
                'group' => 'general',
                'type' => 'textarea',
                'label' => 'Site Address',
                'description' => 'Business address',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_rate',
                'value' => '18',
                'group' => 'pricing',
                'type' => 'number',
                'label' => 'Tax Rate (%)',
                'description' => 'Default GST/Tax rate percentage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_charge',
                'value' => '50',
                'group' => 'pricing',
                'type' => 'number',
                'label' => 'Shipping Charge',
                'description' => 'Default shipping charge',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '1000',
                'group' => 'pricing',
                'type' => 'number',
                'label' => 'Free Shipping Threshold',
                'description' => 'Order amount for free shipping',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => '₹',
                'group' => 'pricing',
                'type' => 'text',
                'label' => 'Currency Symbol',
                'description' => 'Currency symbol to display',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'low_stock_threshold',
                'value' => '10',
                'group' => 'inventory',
                'type' => 'number',
                'label' => 'Low Stock Threshold',
                'description' => 'Alert when stock falls below this number',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'dealer_min_order',
                'value' => '500',
                'group' => 'dealer',
                'type' => 'number',
                'label' => 'Dealer Minimum Order',
                'description' => 'Minimum order value for dealers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'payment_methods',
                'value' => '["Cash on Delivery", "Bank Transfer", "UPI", "Credit/Debit Card"]',
                'group' => 'payment',
                'type' => 'json',
                'label' => 'Payment Methods',
                'description' => 'Available payment methods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};


















