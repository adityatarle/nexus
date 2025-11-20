<?php

namespace App\Imports;

use App\Models\AgricultureProduct;
use App\Models\AgricultureCategory;
use App\Models\AgricultureSubcategory;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading, WithEvents
{
    use SkipsFailures;

    protected $errors = [];
    protected $info = []; // Info messages (like category auto-creation)
    protected $successCount = 0;
    protected $failCount = 0;
    protected $rowNumber = 1; // Start at 1 (header row), will increment for each data row
    protected $createdCategories = []; // Cache for created categories to avoid duplicate lookups
    protected $createdSubcategories = []; // Cache for created subcategories to avoid duplicate lookups
    protected $totalRowsProcessed = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowNumber++; // Increment row number (header is row 1, first data row is 2)
        $this->totalRowsProcessed++;
        
        \Log::info("Product Import - Processing row {$this->rowNumber}", [
            'row_data' => $row,
            'columns' => array_keys($row),
            'row_count' => count($row)
        ]);
        
        try {
            // Debug: Log available columns on first row
            if ($this->rowNumber == 2) {
                \Log::info('Product Import - Available columns', ['columns' => array_keys($row)]);
            }
            
            // Check if row is empty
            $hasData = false;
            foreach ($row as $value) {
                if (!empty($value) && trim($value) !== '') {
                    $hasData = true;
                    break;
                }
            }
            
            if (!$hasData) {
                \Log::info("Product Import - Row {$this->rowNumber} is empty, skipping");
                return null;
            }
            
            // Normalize column names (handle case sensitivity, spaces, asterisks, and parentheses)
            $normalizedRow = [];
            foreach ($row as $key => $value) {
                // Remove asterisks, parentheses, and normalize
                $cleanKey = preg_replace('/[^\w\s]/', '', $key); // Remove special chars except word chars and spaces
                $normalizedKey = strtolower(trim(str_replace([' ', '_'], '', $cleanKey)));
                $normalizedRow[$normalizedKey] = $value;
            }
            
            \Log::info("Product Import - Normalized columns", [
                'row_number' => $this->rowNumber,
                'original_keys' => array_keys($row),
                'normalized_keys' => array_keys($normalizedRow)
            ]);
            
            // Map normalized keys to expected keys - handle various formats
            $categoryKey = null;
            
            // Try normalized first
            if (isset($normalizedRow['category'])) {
                // Find the original key that matches
                foreach ($row as $origKey => $val) {
                    $cleanOrig = preg_replace('/[^\w\s]/', '', $origKey);
                    $normOrig = strtolower(trim(str_replace([' ', '_'], '', $cleanOrig)));
                    if ($normOrig === 'category') {
                        $categoryKey = $origKey;
                        break;
                    }
                }
            }
            
            // Try exact matches
            if (!$categoryKey) {
                foreach (array_keys($row) as $key) {
                    $cleanKey = preg_replace('/[^\w\s]/', '', $key);
                    $normKey = strtolower(trim(str_replace([' ', '_'], '', $cleanKey)));
                    if ($normKey === 'category' || stripos($key, 'category') !== false) {
                        $categoryKey = $key;
                        break;
                    }
                }
            }
            
            if (!$categoryKey || !isset($row[$categoryKey])) {
                $availableCols = implode(', ', array_keys($row));
                \Log::error("Product Import - Category column not found", [
                    'row_number' => $this->rowNumber,
                    'available_columns' => $availableCols,
                    'normalized_keys' => array_keys($normalizedRow)
                ]);
                throw new \Exception("Category column not found. Available columns: " . $availableCols);
            }
            
            // Get or create category by name
            $categoryName = trim($row[$categoryKey]);
            
            if (empty($categoryName)) {
                throw new \Exception("Category is required for product import");
            }
            
            // Check cache first
            if (isset($this->createdCategories[$categoryName])) {
                $category = $this->createdCategories[$categoryName];
            } else {
                // Try to find existing category - use where closure to avoid orWhere issues
                $categorySlug = Str::slug($categoryName);
                $category = AgricultureCategory::where(function($query) use ($categoryName, $categorySlug) {
                    $query->where('name', $categoryName)
                          ->orWhere('slug', $categorySlug);
                })->first();

                // If category doesn't exist, create it
                if (!$category) {
                    // Ensure unique slug
                    $baseSlug = $categorySlug;
                    $counter = 1;
                    while (AgricultureCategory::where('slug', $categorySlug)->exists()) {
                        $categorySlug = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    $category = AgricultureCategory::create([
                        'name' => $categoryName,
                        'slug' => $categorySlug,
                        'description' => "Auto-created during product import",
                        'is_active' => true,
                        'sort_order' => 0,
                    ]);
                    
                    $this->info[] = "Row {$this->rowNumber}: Category '{$categoryName}' was created automatically.";
                }
                
                // Cache the category
                $this->createdCategories[$categoryName] = $category;
            }

            // Handle subcategory (optional)
            $subcategory = null;
            $subcategoryKey = null;
            if (isset($normalizedRow['subcategory'])) {
                $subcategoryKey = 'subcategory';
            } elseif (isset($row['Subcategory'])) {
                $subcategoryKey = 'Subcategory';
            } elseif (isset($row['SUBCATEGORY'])) {
                $subcategoryKey = 'SUBCATEGORY';
            } else {
                // Try to find subcategory column
                foreach (array_keys($row) as $key) {
                    if (stripos($key, 'subcategory') !== false) {
                        $subcategoryKey = $key;
                        break;
                    }
                }
            }
            
            if ($subcategoryKey && isset($row[$subcategoryKey]) && !empty($row[$subcategoryKey]) && trim($row[$subcategoryKey]) !== '') {
                $subcategoryName = trim($row[$subcategoryKey]);
                $cacheKey = $category->id . '_' . $subcategoryName;
                
                // Check cache first
                if (isset($this->createdSubcategories[$cacheKey])) {
                    $subcategory = $this->createdSubcategories[$cacheKey];
                } else {
                    // Try to find existing subcategory within the same category
                    $subcategorySlug = Str::slug($subcategoryName);
                    $subcategory = AgricultureSubcategory::where('agriculture_category_id', $category->id)
                        ->where(function($query) use ($subcategoryName, $subcategorySlug) {
                            $query->where('name', $subcategoryName)
                                  ->orWhere('slug', $subcategorySlug);
                        })
                        ->first();

                    // If subcategory doesn't exist, create it
                    if (!$subcategory) {
                        // Ensure unique slug by including category slug
                        $baseSlug = Str::slug($category->name) . '-' . $subcategorySlug;
                        $finalSlug = $baseSlug;
                        $counter = 1;
                        
                        // Check if slug exists globally (since slug is unique)
                        while (AgricultureSubcategory::where('slug', $finalSlug)->exists()) {
                            $finalSlug = $baseSlug . '-' . $counter;
                            $counter++;
                        }
                        
                        try {
                            $subcategory = AgricultureSubcategory::create([
                                'name' => $subcategoryName,
                                'slug' => $finalSlug,
                                'description' => "Auto-created during product import",
                                'agriculture_category_id' => $category->id,
                                'is_active' => true,
                                'sort_order' => 0,
                            ]);
                            
                            $this->info[] = "Row {$this->rowNumber}: Subcategory '{$subcategoryName}' was created automatically under category '{$categoryName}'.";
                        } catch (\Exception $e) {
                            // If creation fails, log error but continue
                            $this->errors[] = "Row {$this->rowNumber}: Failed to create subcategory '{$subcategoryName}': " . $e->getMessage();
                            $subcategory = null;
                        }
                    }
                    
                    // Cache the subcategory only if it was successfully created/found
                    if ($subcategory) {
                        $this->createdSubcategories[$cacheKey] = $subcategory;
                    }
                }
            }

            // Find SKU column - handle normalized keys
            $skuKey = null;
            if (isset($normalizedRow['sku'])) {
                foreach ($row as $origKey => $val) {
                    $cleanOrig = preg_replace('/[^\w\s]/', '', $origKey);
                    $normOrig = strtolower(trim(str_replace([' ', '_'], '', $cleanOrig)));
                    if ($normOrig === 'sku') {
                        $skuKey = $origKey;
                        break;
                    }
                }
            }
            
            if (!$skuKey) {
                foreach (array_keys($row) as $key) {
                    $cleanKey = preg_replace('/[^\w\s]/', '', $key);
                    $normKey = strtolower(trim(str_replace([' ', '_'], '', $cleanKey)));
                    if ($normKey === 'sku' || stripos($key, 'sku') !== false) {
                        $skuKey = $key;
                        break;
                    }
                }
            }
            
            if (!$skuKey || !isset($row[$skuKey])) {
                throw new \Exception("SKU column not found. Available columns: " . implode(', ', array_keys($row)));
            }
            
            // Check if SKU already exists
            $skuValue = strtoupper(trim($row[$skuKey]));
            $existingProduct = AgricultureProduct::where('sku', $skuValue)->first();
            if ($existingProduct) {
                $productName = isset($row['name']) ? $row['name'] : (isset($row['Name']) ? $row['Name'] : 'Unknown');
                $this->errors[] = "Row {$this->rowNumber}: SKU '{$skuValue}' already exists. Skipping product '{$productName}'.";
                $this->failCount++;
                return null;
            }

            // Find name column - handle normalized keys
            $nameKey = null;
            if (isset($normalizedRow['name'])) {
                foreach ($row as $origKey => $val) {
                    $cleanOrig = preg_replace('/[^\w\s]/', '', $origKey);
                    $normOrig = strtolower(trim(str_replace([' ', '_'], '', $cleanOrig)));
                    if ($normOrig === 'name') {
                        $nameKey = $origKey;
                        break;
                    }
                }
            }
            
            if (!$nameKey) {
                foreach (array_keys($row) as $key) {
                    $cleanKey = preg_replace('/[^\w\s]/', '', $key);
                    $normKey = strtolower(trim(str_replace([' ', '_'], '', $cleanKey)));
                    if ($normKey === 'name' || (stripos($key, 'name') !== false && stripos($key, 'product') === false)) {
                        $nameKey = $key;
                        break;
                    }
                }
            }
            
            if (!$nameKey || !isset($row[$nameKey])) {
                throw new \Exception("Name column not found. Available columns: " . implode(', ', array_keys($row)));
            }

            // Ensure unique product slug
            $productSlug = Str::slug(trim($row[$nameKey]));
            $baseProductSlug = $productSlug;
            $slugCounter = 1;
            while (AgricultureProduct::where('slug', $productSlug)->exists()) {
                $productSlug = $baseProductSlug . '-' . $slugCounter;
                $slugCounter++;
            }

            // Helper function to get column value with fallback - handles normalized keys
            $getValue = function($keys, $default = null) use ($row, $normalizedRow) {
                foreach ($keys as $key) {
                    // Try exact match first
                    if (isset($row[$key])) {
                        return $row[$key];
                    }
                    
                    // Try normalized match
                    $cleanKey = preg_replace('/[^\w\s]/', '', $key);
                    $normalized = strtolower(trim(str_replace([' ', '_'], '', $cleanKey)));
                    if (isset($normalizedRow[$normalized])) {
                        return $normalizedRow[$normalized];
                    }
                }
                
                // Try case-insensitive partial match
                foreach (array_keys($row) as $rowKey) {
                    foreach ($keys as $searchKey) {
                        if (stripos($rowKey, $searchKey) !== false) {
                            return $row[$rowKey];
                        }
                    }
                }
                
                return $default;
            };

            // Prepare data with flexible column name matching
            $data = [
                'name' => trim($row[$nameKey]),
                'slug' => $productSlug,
                'description' => $getValue(['description', 'Description', 'DESCRIPTION'], ''),
                'short_description' => $getValue(['short_description', 'Short Description', 'SHORT_DESCRIPTION']) ? trim($getValue(['short_description', 'Short Description', 'SHORT_DESCRIPTION'])) : null,
                'price' => (float) $getValue(['price', 'Price', 'PRICE'], 0),
                'sale_price' => $getValue(['sale_price', 'Sale Price', 'SALE_PRICE']) && $getValue(['sale_price', 'Sale Price', 'SALE_PRICE']) !== '' ? (float) $getValue(['sale_price', 'Sale Price', 'SALE_PRICE']) : null,
                'dealer_price' => $getValue(['dealer_price', 'Dealer Price', 'DEALER_PRICE']) ? (float) $getValue(['dealer_price', 'Dealer Price', 'DEALER_PRICE']) : (float) $getValue(['price', 'Price', 'PRICE'], 0),
                'dealer_sale_price' => $getValue(['dealer_sale_price', 'Dealer Sale Price', 'DEALER_SALE_PRICE']) && $getValue(['dealer_sale_price', 'Dealer Sale Price', 'DEALER_SALE_PRICE']) !== '' ? (float) $getValue(['dealer_sale_price', 'Dealer Sale Price', 'DEALER_SALE_PRICE']) : null,
                'sku' => $skuValue,
                'stock_quantity' => (int) ($getValue(['stock_quantity', 'Stock Quantity', 'STOCK_QUANTITY'], 0)),
                'manage_stock' => $getValue(['manage_stock', 'Manage Stock', 'MANAGE_STOCK']) !== null ? (bool) $getValue(['manage_stock', 'Manage Stock', 'MANAGE_STOCK']) : true,
                'in_stock' => $getValue(['in_stock', 'In Stock', 'IN_STOCK']) !== null ? (bool) $getValue(['in_stock', 'In Stock', 'IN_STOCK']) : true,
                'weight' => $getValue(['weight', 'Weight', 'WEIGHT']) && $getValue(['weight', 'Weight', 'WEIGHT']) !== '' ? (float) $getValue(['weight', 'Weight', 'WEIGHT']) : null,
                'dimensions' => $getValue(['dimensions', 'Dimensions', 'DIMENSIONS']) ? trim($getValue(['dimensions', 'Dimensions', 'DIMENSIONS'])) : null,
                'brand' => $getValue(['brand', 'Brand', 'BRAND']) ? trim($getValue(['brand', 'Brand', 'BRAND'])) : null,
                'model' => $getValue(['model', 'Model', 'MODEL']) ? trim($getValue(['model', 'Model', 'MODEL'])) : null,
                'power_source' => $getValue(['power_source', 'Power Source', 'POWER_SOURCE']) ? trim($getValue(['power_source', 'Power Source', 'POWER_SOURCE'])) : null,
                'warranty' => $getValue(['warranty', 'Warranty', 'WARRANTY']) ? trim($getValue(['warranty', 'Warranty', 'WARRANTY'])) : null,
                'is_featured' => $getValue(['is_featured', 'Is Featured', 'IS_FEATURED']) !== null ? (bool) $getValue(['is_featured', 'Is Featured', 'IS_FEATURED']) : false,
                'is_active' => $getValue(['is_active', 'Is Active', 'IS_ACTIVE']) !== null ? (bool) $getValue(['is_active', 'Is Active', 'IS_ACTIVE']) : true,
                'is_dealer_exclusive' => $getValue(['is_dealer_exclusive', 'Is Dealer Exclusive', 'IS_DEALER_EXCLUSIVE']) !== null ? (bool) $getValue(['is_dealer_exclusive', 'Is Dealer Exclusive', 'IS_DEALER_EXCLUSIVE']) : false,
                'dealer_min_quantity' => (int) ($getValue(['dealer_min_quantity', 'Dealer Min Quantity', 'DEALER_MIN_QUANTITY'], 1)),
                'dealer_notes' => $getValue(['dealer_notes', 'Dealer Notes', 'DEALER_NOTES']) ? trim($getValue(['dealer_notes', 'Dealer Notes', 'DEALER_NOTES'])) : null,
                'agriculture_category_id' => $category->id,
                'agriculture_subcategory_id' => $subcategory ? $subcategory->id : null,
            ];

            // Calculate dealer discount percentage
            if (isset($data['price']) && isset($data['dealer_price']) && $data['price'] > 0) {
                $discount = (($data['price'] - $data['dealer_price']) / $data['price']) * 100;
                $data['dealer_discount_percentage'] = round($discount, 2);
            } else {
                $data['dealer_discount_percentage'] = 0;
            }

            // Remove any fields not in fillable to prevent mass assignment errors
            $fillableFields = (new AgricultureProduct())->getFillable();
            $filteredData = array_intersect_key($data, array_flip($fillableFields));
            
            // Ensure all required fields are present
            if (empty($filteredData['name']) || empty($filteredData['sku']) || empty($filteredData['price'])) {
                throw new \Exception("Missing required fields: name, sku, or price");
            }

            // Try to create the product
            try {
                $product = new AgricultureProduct($filteredData);
                $this->successCount++;
                
                \Log::info("Product Import - Product created successfully", [
                    'row_number' => $this->rowNumber,
                    'product_name' => $filteredData['name'],
                    'sku' => $filteredData['sku']
                ]);
                
                return $product;
            } catch (\Exception $createException) {
                \Log::error("Product Import - Failed to create product model", [
                    'row_number' => $this->rowNumber,
                    'error' => $createException->getMessage(),
                    'data' => $filteredData
                ]);
                throw $createException;
            }
        } catch (\Exception $e) {
            $productName = $row['name'] ?? 'Unknown';
            $errorMsg = "Row {$this->rowNumber}: Error importing '{$productName}': " . $e->getMessage();
            $this->errors[] = $errorMsg;
            $this->failCount++;
            
            // Log detailed error for debugging
            \Log::error('Product Import Row Error', [
                'row_number' => $this->rowNumber,
                'product_name' => $productName,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'row_data' => $row
            ]);
            
            return null;
        }
    }
    
    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100; // Process 100 rows at a time for better performance
    }
    
    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100; // Read 100 rows at a time
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        // Use very lenient validation - we'll validate in model() method instead
        // This prevents validation from blocking the import before model() is called
        return [];
    }

    /**
     * @return array
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'sku.required' => 'SKU is required.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'category.required' => 'Category is required.',
        ];
    }

    /**
     * Get import errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get info messages (like category auto-creation)
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * Get success count
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Get failure count
     */
    public function getFailCount(): int
    {
        return $this->failCount;
    }
    
    /**
     * Get total rows processed
     */
    public function getTotalRowsProcessed(): int
    {
        return $this->totalRowsProcessed;
    }
    
    /**
     * Register events
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                \Log::info('Product Import - Before import', [
                    'reader_type' => get_class($event->getReader()),
                ]);
            },
            AfterImport::class => function(AfterImport $event) {
                \Log::info('Product Import - After import', [
                    'total_rows_processed' => $this->totalRowsProcessed,
                    'success_count' => $this->successCount,
                    'fail_count' => $this->failCount,
                ]);
            },
        ];
    }
}

