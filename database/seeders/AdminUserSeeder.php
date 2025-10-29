<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AgricultureProduct;
use App\Models\AgricultureCategory;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@nexusagriculture.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
        ]);

        // Create Sample Customer
        User::create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1234567891',
        ]);

        // Create Sample Dealer (Pending)
        User::create([
            'name' => 'Jane Dealer',
            'email' => 'dealer@example.com',
            'password' => Hash::make('password'),
            'role' => 'dealer',
            'phone' => '+1234567892',
            'business_name' => 'Green Farm Supplies',
            'gst_number' => '22ABCDE1234F1Z5',
            'business_address' => '123 Farm Street, Agriculture City',
            'contact_person' => 'Jane Dealer',
            'is_dealer_approved' => false,
        ]);

        // Create Sample Approved Dealer
        User::create([
            'name' => 'Mike Wholesaler',
            'email' => 'wholesaler@example.com',
            'password' => Hash::make('password'),
            'role' => 'dealer',
            'phone' => '+1234567893',
            'business_name' => 'Agri Wholesale Co.',
            'gst_number' => '22FGHIJ5678K2L6',
            'business_address' => '456 Wholesale Avenue, Business District',
            'contact_person' => 'Mike Wholesaler',
            'is_dealer_approved' => true,
            'dealer_approved_at' => now(),
        ]);

        // Update existing agriculture products with dealer pricing
        $products = AgricultureProduct::all();
        foreach ($products as $product) {
            // Set dealer price (typically 20-30% discount)
            $dealerPrice = $product->price * 0.75; // 25% discount
            $dealerSalePrice = $product->sale_price ? $product->sale_price * 0.75 : null;
            
            // Set bulk pricing tiers
            $bulkPricingTiers = [
                ['min_quantity' => 10, 'price' => $dealerPrice * 0.95], // 5% additional discount for 10+
                ['min_quantity' => 25, 'price' => $dealerPrice * 0.90], // 10% additional discount for 25+
                ['min_quantity' => 50, 'price' => $dealerPrice * 0.85], // 15% additional discount for 50+
            ];

            $product->update([
                'dealer_price' => $dealerPrice,
                'dealer_sale_price' => $dealerSalePrice,
                'bulk_pricing_tiers' => $bulkPricingTiers,
                'dealer_min_quantity' => 1,
                'is_dealer_exclusive' => false,
                'dealer_notes' => 'Available for wholesale purchase with volume discounts.',
            ]);
        }

        $this->command->info('Admin users and sample data created successfully!');
        $this->command->info('Admin Login: admin@nexusagriculture.com / password');
        $this->command->info('Customer Login: customer@example.com / password');
        $this->command->info('Dealer Login: dealer@example.com / password');
        $this->command->info('Approved Dealer Login: wholesaler@example.com / password');
    }
}