<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgricultureProduct;
use App\Models\AgricultureCategory;

class AgricultureProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = AgricultureCategory::all();
        
        $products = [
            // Tractors & Heavy Machinery
            [
                'name' => 'John Deere 6R Series Tractor',
                'slug' => 'john-deere-6r-series-tractor',
                'description' => 'Powerful 6R Series tractor with advanced technology for modern farming operations.',
                'short_description' => 'Advanced tractor with precision farming capabilities',
                'price' => 125000.00,
                'sale_price' => 115000.00,
                'dealer_price' => 110000.00,
                'dealer_sale_price' => 105000.00,
                'sku' => 'JD-6R-2024',
                'stock_quantity' => 5,
                'brand' => 'John Deere',
                'model' => '6R Series',
                'power_source' => 'Diesel',
                'warranty' => '3 Years',
                'is_featured' => true,
                'agriculture_category_id' => $categories->where('slug', 'tractors-heavy-machinery')->first()->id
            ],
            [
                'name' => 'Case IH Magnum Tractor',
                'slug' => 'case-ih-magnum-tractor',
                'description' => 'Heavy-duty Magnum tractor designed for large-scale agricultural operations.',
                'short_description' => 'Heavy-duty tractor for large farms',
                'price' => 145000.00,
                'sku' => 'CI-MAG-2024',
                'stock_quantity' => 3,
                'brand' => 'Case IH',
                'model' => 'Magnum',
                'power_source' => 'Diesel',
                'warranty' => '3 Years',
                'is_featured' => true,
                'agriculture_category_id' => $categories->where('slug', 'tractors-heavy-machinery')->first()->id
            ],
            
            // Planting & Seeding Equipment
            [
                'name' => 'Precision Seed Drill 24-Row',
                'slug' => 'precision-seed-drill-24-row',
                'description' => 'High-precision seed drill for accurate planting with 24-row configuration.',
                'short_description' => '24-row precision seed drill',
                'price' => 45000.00,
                'sale_price' => 42000.00,
                'sku' => 'PSD-24R-2024',
                'stock_quantity' => 8,
                'brand' => 'Precision Ag',
                'model' => 'PSD-24R',
                'power_source' => 'Tractor PTO',
                'warranty' => '2 Years',
                'is_featured' => true,
                'agriculture_category_id' => $categories->where('slug', 'planting-seeding-equipment')->first()->id
            ],
            [
                'name' => 'Air Seeder System',
                'slug' => 'air-seeder-system',
                'description' => 'Advanced air seeder system for efficient seed distribution and planting.',
                'short_description' => 'Advanced air seeder system',
                'price' => 75000.00,
                'sku' => 'ASS-2024',
                'stock_quantity' => 4,
                'brand' => 'Bourgault',
                'model' => 'Air Seeder',
                'power_source' => 'Tractor PTO',
                'warranty' => '2 Years',
                'agriculture_category_id' => $categories->where('slug', 'planting-seeding-equipment')->first()->id
            ],
            
            // Harvesting Equipment
            [
                'name' => 'Combine Harvester Class Lexion',
                'slug' => 'combine-harvester-class-lexion',
                'description' => 'High-performance combine harvester with advanced grain processing technology.',
                'short_description' => 'High-performance combine harvester',
                'price' => 350000.00,
                'sale_price' => 325000.00,
                'sku' => 'CH-CL-2024',
                'stock_quantity' => 2,
                'brand' => 'Class',
                'model' => 'Lexion',
                'power_source' => 'Diesel',
                'warranty' => '3 Years',
                'is_featured' => true,
                'agriculture_category_id' => $categories->where('slug', 'harvesting-equipment')->first()->id
            ],
            [
                'name' => 'Forage Harvester',
                'slug' => 'forage-harvester',
                'description' => 'Efficient forage harvester for silage and hay production.',
                'short_description' => 'Efficient forage harvester',
                'price' => 180000.00,
                'sku' => 'FH-2024',
                'stock_quantity' => 3,
                'brand' => 'New Holland',
                'model' => 'Forage Harvester',
                'power_source' => 'Diesel',
                'warranty' => '2 Years',
                'agriculture_category_id' => $categories->where('slug', 'harvesting-equipment')->first()->id
            ],
            
            // Irrigation Systems
            [
                'name' => 'Center Pivot Irrigation System',
                'slug' => 'center-pivot-irrigation-system',
                'description' => 'Automated center pivot irrigation system for efficient water distribution.',
                'short_description' => 'Automated center pivot irrigation',
                'price' => 85000.00,
                'sku' => 'CPI-2024',
                'stock_quantity' => 6,
                'brand' => 'Valley',
                'model' => 'Center Pivot',
                'power_source' => 'Electric',
                'warranty' => '5 Years',
                'is_featured' => true,
                'agriculture_category_id' => $categories->where('slug', 'irrigation-systems')->first()->id
            ],
            [
                'name' => 'Drip Irrigation Kit',
                'slug' => 'drip-irrigation-kit',
                'description' => 'Complete drip irrigation kit for water-efficient crop watering.',
                'short_description' => 'Complete drip irrigation kit',
                'price' => 2500.00,
                'sale_price' => 2200.00,
                'sku' => 'DIK-2024',
                'stock_quantity' => 25,
                'brand' => 'Netafim',
                'model' => 'Drip Kit',
                'power_source' => 'Manual',
                'warranty' => '1 Year',
                'agriculture_category_id' => $categories->where('slug', 'irrigation-systems')->first()->id
            ],
            
            // Soil Preparation Tools
            [
                'name' => 'Moldboard Plow 5-Bottom',
                'slug' => 'moldboard-plow-5-bottom',
                'description' => 'Heavy-duty moldboard plow for deep soil preparation.',
                'short_description' => '5-bottom moldboard plow',
                'price' => 12000.00,
                'sku' => 'MP-5B-2024',
                'stock_quantity' => 10,
                'brand' => 'Kverneland',
                'model' => 'Moldboard Plow',
                'power_source' => 'Tractor PTO',
                'warranty' => '1 Year',
                'agriculture_category_id' => $categories->where('slug', 'soil-preparation-tools')->first()->id
            ],
            [
                'name' => 'Rotary Tiller',
                'slug' => 'rotary-tiller',
                'description' => 'High-performance rotary tiller for soil preparation and cultivation.',
                'short_description' => 'High-performance rotary tiller',
                'price' => 8500.00,
                'sku' => 'RT-2024',
                'stock_quantity' => 15,
                'brand' => 'Howard',
                'model' => 'Rotary Tiller',
                'power_source' => 'Tractor PTO',
                'warranty' => '1 Year',
                'agriculture_category_id' => $categories->where('slug', 'soil-preparation-tools')->first()->id
            ],
            
            // Livestock Equipment
            [
                'name' => 'Automatic Milking System',
                'slug' => 'automatic-milking-system',
                'description' => 'Advanced automatic milking system for dairy operations.',
                'short_description' => 'Automatic milking system',
                'price' => 95000.00,
                'sku' => 'AMS-2024',
                'stock_quantity' => 4,
                'brand' => 'DeLaval',
                'model' => 'Milking System',
                'power_source' => 'Electric',
                'warranty' => '3 Years',
                'is_featured' => true,
                'agriculture_category_id' => $categories->where('slug', 'livestock-equipment')->first()->id
            ],
            [
                'name' => 'Feed Mixer Wagon',
                'slug' => 'feed-mixer-wagon',
                'description' => 'High-capacity feed mixer wagon for livestock feeding.',
                'short_description' => 'High-capacity feed mixer',
                'price' => 35000.00,
                'sku' => 'FMW-2024',
                'stock_quantity' => 7,
                'brand' => 'Kuhn',
                'model' => 'Feed Mixer',
                'power_source' => 'Tractor PTO',
                'warranty' => '2 Years',
                'agriculture_category_id' => $categories->where('slug', 'livestock-equipment')->first()->id
            ],
            
            // Fertilizer & Spraying
            [
                'name' => 'Fertilizer Spreader 3-Point',
                'slug' => 'fertilizer-spreader-3-point',
                'description' => 'Precision fertilizer spreader for accurate nutrient application.',
                'short_description' => '3-point fertilizer spreader',
                'price' => 15000.00,
                'sku' => 'FS-3P-2024',
                'stock_quantity' => 12,
                'brand' => 'Amazone',
                'model' => 'Fertilizer Spreader',
                'power_source' => 'Tractor PTO',
                'warranty' => '1 Year',
                'agriculture_category_id' => $categories->where('slug', 'fertilizer-spraying')->first()->id
            ],
            [
                'name' => 'Crop Sprayer Boom',
                'slug' => 'crop-sprayer-boom',
                'description' => 'High-capacity crop sprayer boom for pest and disease control.',
                'short_description' => 'High-capacity crop sprayer',
                'price' => 28000.00,
                'sku' => 'CSB-2024',
                'stock_quantity' => 8,
                'brand' => 'Spra-Coupe',
                'model' => 'Crop Sprayer',
                'power_source' => 'Tractor PTO',
                'warranty' => '2 Years',
                'agriculture_category_id' => $categories->where('slug', 'fertilizer-spraying')->first()->id
            ],
            
            // Farm Tools & Accessories
            [
                'name' => 'Farm Tool Set Professional',
                'slug' => 'farm-tool-set-professional',
                'description' => 'Complete professional farm tool set for various agricultural tasks.',
                'short_description' => 'Professional farm tool set',
                'price' => 450.00,
                'sale_price' => 399.00,
                'sku' => 'FTS-PRO-2024',
                'stock_quantity' => 50,
                'brand' => 'FarmPro',
                'model' => 'Tool Set',
                'power_source' => 'Manual',
                'warranty' => '1 Year',
                'agriculture_category_id' => $categories->where('slug', 'farm-tools-accessories')->first()->id
            ],
            [
                'name' => 'Hydraulic Cylinder Repair Kit',
                'slug' => 'hydraulic-cylinder-repair-kit',
                'description' => 'Complete hydraulic cylinder repair kit for maintenance.',
                'short_description' => 'Hydraulic repair kit',
                'price' => 125.00,
                'sku' => 'HCRK-2024',
                'stock_quantity' => 100,
                'brand' => 'HydraTech',
                'model' => 'Repair Kit',
                'power_source' => 'Manual',
                'warranty' => '6 Months',
                'agriculture_category_id' => $categories->where('slug', 'farm-tools-accessories')->first()->id
            ]
        ];

        foreach ($products as $product) {
            // Auto-generate dealer prices if not set (typically 10-15% less than retail)
            if (!isset($product['dealer_price'])) {
                $product['dealer_price'] = round($product['price'] * 0.88, 2); // 12% discount
            }
            if (!isset($product['dealer_sale_price']) && isset($product['sale_price'])) {
                $product['dealer_sale_price'] = round($product['sale_price'] * 0.88, 2); // 12% discount
            }
            
            AgricultureProduct::create($product);
        }
    }
}