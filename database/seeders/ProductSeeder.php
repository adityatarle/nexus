<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $fruitsCategory = Category::where('slug', 'fruits')->first();
        $vegetablesCategory = Category::where('slug', 'vegetables')->first();
        $dairyCategory = Category::where('slug', 'dairy')->first();
        $meatCategory = Category::where('slug', 'meat-seafood')->first();

        $products = [
            // Fruits
            [
                'name' => 'Organic Apples',
                'slug' => 'organic-apples',
                'description' => 'Fresh organic apples from local farms',
                'short_description' => 'Crisp and sweet organic apples',
                'price' => 4.99,
                'sale_price' => 3.99,
                'sku' => 'FRUIT-001',
                'stock_quantity' => 100,
                'featured_image' => 'product-thumb-1.png',
                'is_featured' => true,
                'category_id' => $fruitsCategory->id,
            ],
            [
                'name' => 'Organic Bananas',
                'slug' => 'organic-bananas',
                'description' => 'Fresh organic bananas',
                'short_description' => 'Sweet and nutritious bananas',
                'price' => 2.99,
                'sku' => 'FRUIT-002',
                'stock_quantity' => 150,
                'featured_image' => 'product-thumb-2.png',
                'is_featured' => true,
                'category_id' => $fruitsCategory->id,
            ],
            [
                'name' => 'Organic Oranges',
                'slug' => 'organic-oranges',
                'description' => 'Fresh organic oranges',
                'short_description' => 'Juicy and vitamin-rich oranges',
                'price' => 3.99,
                'sku' => 'FRUIT-003',
                'stock_quantity' => 80,
                'featured_image' => 'product-thumb-3.png',
                'category_id' => $fruitsCategory->id,
            ],
            [
                'name' => 'Organic Strawberries',
                'slug' => 'organic-strawberries',
                'description' => 'Fresh organic strawberries',
                'short_description' => 'Sweet and aromatic strawberries',
                'price' => 6.99,
                'sku' => 'FRUIT-004',
                'stock_quantity' => 60,
                'featured_image' => 'product-thumb-4.png',
                'is_featured' => true,
                'category_id' => $fruitsCategory->id,
            ],

            // Vegetables
            [
                'name' => 'Organic Carrots',
                'slug' => 'organic-carrots',
                'description' => 'Fresh organic carrots',
                'short_description' => 'Crunchy and nutritious carrots',
                'price' => 2.49,
                'sku' => 'VEG-001',
                'stock_quantity' => 120,
                'featured_image' => 'product-thumb-5.png',
                'is_featured' => true,
                'category_id' => $vegetablesCategory->id,
            ],
            [
                'name' => 'Organic Tomatoes',
                'slug' => 'organic-tomatoes',
                'description' => 'Fresh organic tomatoes',
                'short_description' => 'Juicy and flavorful tomatoes',
                'price' => 3.99,
                'sku' => 'VEG-002',
                'stock_quantity' => 90,
                'featured_image' => 'product-thumb-6.png',
                'category_id' => $vegetablesCategory->id,
            ],
            [
                'name' => 'Organic Spinach',
                'slug' => 'organic-spinach',
                'description' => 'Fresh organic spinach leaves',
                'short_description' => 'Nutrient-rich spinach',
                'price' => 2.99,
                'sku' => 'VEG-003',
                'stock_quantity' => 70,
                'featured_image' => 'product-thumb-7.png',
                'is_featured' => true,
                'category_id' => $vegetablesCategory->id,
            ],
            [
                'name' => 'Organic Broccoli',
                'slug' => 'organic-broccoli',
                'description' => 'Fresh organic broccoli',
                'short_description' => 'Healthy and delicious broccoli',
                'price' => 4.49,
                'sku' => 'VEG-004',
                'stock_quantity' => 85,
                'featured_image' => 'product-thumb-8.png',
                'category_id' => $vegetablesCategory->id,
            ],

            // Dairy
            [
                'name' => 'Organic Milk',
                'slug' => 'organic-milk',
                'description' => 'Fresh organic milk',
                'short_description' => 'Pure and natural milk',
                'price' => 5.99,
                'sku' => 'DAIRY-001',
                'stock_quantity' => 50,
                'featured_image' => 'product-thumb-9.png',
                'is_featured' => true,
                'category_id' => $dairyCategory->id,
            ],
            [
                'name' => 'Organic Cheese',
                'slug' => 'organic-cheese',
                'description' => 'Fresh organic cheese',
                'short_description' => 'Rich and creamy cheese',
                'price' => 7.99,
                'sku' => 'DAIRY-002',
                'stock_quantity' => 40,
                'featured_image' => 'product-thumb-10.png',
                'category_id' => $dairyCategory->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}