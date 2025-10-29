<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fruits',
                'slug' => 'fruits',
                'description' => 'Fresh organic fruits',
                'icon' => 'fruits',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Vegetables',
                'slug' => 'vegetables',
                'description' => 'Fresh organic vegetables',
                'icon' => 'vegetables',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Dairy',
                'slug' => 'dairy',
                'description' => 'Fresh dairy products',
                'icon' => 'dairy',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Meat & Seafood',
                'slug' => 'meat-seafood',
                'description' => 'Fresh meat and seafood',
                'icon' => 'meat',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Bakery',
                'slug' => 'bakery',
                'description' => 'Fresh baked goods',
                'icon' => 'bakery',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Beverages',
                'slug' => 'beverages',
                'description' => 'Organic beverages',
                'icon' => 'beverages',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Snacks',
                'slug' => 'snacks',
                'description' => 'Healthy snacks',
                'icon' => 'snacks',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Spices & Herbs',
                'slug' => 'spices-herbs',
                'description' => 'Organic spices and herbs',
                'icon' => 'spices',
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}