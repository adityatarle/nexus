<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgricultureProduct;

class AddDealerPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Adding dealer pricing to all products...');
        
        $products = AgricultureProduct::all();
        $updatedCount = 0;
        
        foreach ($products as $product) {
            // Calculate dealer price (25% discount from retail price)
            $dealerPrice = round($product->price * 0.75, 2);
            
            // Calculate dealer sale price if sale_price exists
            $dealerSalePrice = null;
            if ($product->sale_price) {
                $dealerSalePrice = round($product->sale_price * 0.75, 2);
            }
            
            // Set bulk pricing tiers for dealers
            $bulkPricingTiers = [
                ['min_quantity' => 10, 'price' => round($dealerPrice * 0.95, 2)], // 5% additional discount for 10+
                ['min_quantity' => 25, 'price' => round($dealerPrice * 0.90, 2)], // 10% additional discount for 25+
                ['min_quantity' => 50, 'price' => round($dealerPrice * 0.85, 2)], // 15% additional discount for 50+
            ];

            $product->update([
                'dealer_price' => $dealerPrice,
                'dealer_sale_price' => $dealerSalePrice,
                'bulk_pricing_tiers' => $bulkPricingTiers,
                'dealer_min_quantity' => 1,
                'is_dealer_exclusive' => false,
                'dealer_notes' => 'Available for wholesale purchase with volume discounts.',
            ]);
            
            $updatedCount++;
        }

        $this->command->info("✓ Updated {$updatedCount} products with dealer pricing!");
        $this->command->info('');
        $this->command->info('Dealer pricing structure:');
        $this->command->info('- Dealer price: 25% off retail price');
        $this->command->info('- Bulk discounts: 5-15% additional discount for quantity orders');
        $this->command->info('');
        
        // Show sample
        $sampleProduct = $products->first();
        if ($sampleProduct) {
            $this->command->info('Sample Product:');
            $this->command->info("  Name: {$sampleProduct->name}");
            $this->command->info("  Retail Price: ₹{$sampleProduct->price}");
            $this->command->info("  Dealer Price: ₹{$sampleProduct->dealer_price}");
            $this->command->info("  Savings: ₹" . ($sampleProduct->price - $sampleProduct->dealer_price));
        }
    }
}

