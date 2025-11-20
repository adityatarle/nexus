<?php

namespace App\Console\Commands;

use App\Models\AgricultureProduct;
use App\Models\AgricultureOrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:clear {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all products from the database for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Show warning
        $this->warn('⚠️  WARNING: This will delete ALL products from the database!');
        $this->newLine();

        // Show current count
        $productCount = AgricultureProduct::count();
        $this->info("Current products count: {$productCount}");
        $this->newLine();

        // Ask for confirmation unless --force is used
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all products?', false)) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Start deletion process
        $this->info('Starting product deletion...');
        $this->newLine();

        try {
            DB::beginTransaction();

            // Delete order items related to products (to avoid foreign key issues)
            $orderItemsCount = AgricultureOrderItem::count();
            if ($orderItemsCount > 0) {
                $this->warn("Found {$orderItemsCount} order items. These will remain but products will be removed.");
            }

            // Delete all products
            $deletedCount = AgricultureProduct::query()->delete();

            DB::commit();

            // Success message
            $this->newLine();
            $this->info("✅ Successfully deleted {$deletedCount} products!");
            $this->newLine();
            $this->line('You can now add products manually through the admin panel:');
            $this->line('👉 http://127.0.0.1:8000/admin/products/create');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->error('❌ Error deleting products: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Products were NOT deleted. Please check the error and try again.');
            
            return 1;
        }
    }
}
















