<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgricultureCategory;

class AgricultureCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tractors & Heavy Machinery',
                'slug' => 'tractors-heavy-machinery',
                'description' => 'Farm tractors, excavators, and heavy agricultural machinery',
                'icon' => 'tractor',
                'sort_order' => 1
            ],
            [
                'name' => 'Planting & Seeding Equipment',
                'slug' => 'planting-seeding-equipment',
                'description' => 'Seed drills, planters, and seeding machinery',
                'icon' => 'seeds',
                'sort_order' => 2
            ],
            [
                'name' => 'Harvesting Equipment',
                'slug' => 'harvesting-equipment',
                'description' => 'Combine harvesters, mowers, and crop collection equipment',
                'icon' => 'harvest',
                'sort_order' => 3
            ],
            [
                'name' => 'Irrigation Systems',
                'slug' => 'irrigation-systems',
                'description' => 'Sprinklers, drip systems, and water management equipment',
                'icon' => 'water',
                'sort_order' => 4
            ],
            [
                'name' => 'Soil Preparation Tools',
                'slug' => 'soil-preparation-tools',
                'description' => 'Plows, cultivators, and soil preparation equipment',
                'icon' => 'soil',
                'sort_order' => 5
            ],
            [
                'name' => 'Livestock Equipment',
                'slug' => 'livestock-equipment',
                'description' => 'Feeding systems, milking machines, and animal care equipment',
                'icon' => 'livestock',
                'sort_order' => 6
            ],
            [
                'name' => 'Fertilizer & Spraying',
                'slug' => 'fertilizer-spraying',
                'description' => 'Fertilizer spreaders, sprayers, and crop protection equipment',
                'icon' => 'spray',
                'sort_order' => 7
            ],
            [
                'name' => 'Farm Tools & Accessories',
                'slug' => 'farm-tools-accessories',
                'description' => 'Hand tools, maintenance equipment, and farm accessories',
                'icon' => 'tools',
                'sort_order' => 8
            ]
        ];

        foreach ($categories as $category) {
            AgricultureCategory::create($category);
        }
    }
}