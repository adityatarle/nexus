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
        // Add indexes to agriculture_products table
        Schema::table('agriculture_products', function (Blueprint $table) {
            // Composite index for active products with stock
            $table->index(['is_active', 'stock_quantity'], 'idx_products_active_stock');
            
            // Composite index for category and active status
            $table->index(['agriculture_category_id', 'is_active'], 'idx_products_category_active');
            
            // Index for brand filtering
            $table->index('brand', 'idx_products_brand');
            
            // Index for power source filtering
            $table->index('power_source', 'idx_products_power_source');
            
            // Index for featured products
            $table->index('is_featured', 'idx_products_featured');
            
            // Index for sorting by created date
            $table->index('created_at', 'idx_products_created');
            
            // Index for price sorting
            $table->index('price', 'idx_products_price');
            $table->index('sale_price', 'idx_products_sale_price');
            
            // Index for dealer pricing
            $table->index('dealer_price', 'idx_products_dealer_price');
        });

        // Add indexes to agriculture_orders table
        Schema::table('agriculture_orders', function (Blueprint $table) {
            // Composite index for user orders by status
            $table->index(['user_id', 'order_status'], 'idx_orders_user_status');
            
            // Composite index for order and payment status
            $table->index(['order_status', 'payment_status'], 'idx_orders_status');
            
            // Index for order date sorting
            $table->index('created_at', 'idx_orders_created');
            
            // Index for order number lookups
            $table->index('order_number', 'idx_orders_number');
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            // Composite index for role and dealer approval
            $table->index(['role', 'is_dealer_approved'], 'idx_users_role_dealer');
            
            // Index for email lookups (if not already unique)
            if (!Schema::hasColumn('users', 'email')) {
                $table->index('email', 'idx_users_email');
            }
        });

        // Add indexes to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            // Composite index for user notifications by read status
            $table->index(['user_id', 'read_at'], 'idx_notifications_user_read');
            
            // Index for unread notifications
            $table->index('read_at', 'idx_notifications_read');
            
            // Index for notification type
            $table->index('type', 'idx_notifications_type');
            
            // Index for created date
            $table->index('created_at', 'idx_notifications_created');
        });

        // Add indexes to agriculture_order_items table
        Schema::table('agriculture_order_items', function (Blueprint $table) {
            // Index for order items lookup
            $table->index('agriculture_order_id', 'idx_order_items_order');
            
            // Index for product items lookup
            $table->index('agriculture_product_id', 'idx_order_items_product');
        });

        // Add indexes to agriculture_categories table
        Schema::table('agriculture_categories', function (Blueprint $table) {
            // Index for active categories
            $table->index('is_active', 'idx_categories_active');
            
            // Index for sorting
            $table->index('sort_order', 'idx_categories_sort');
        });

        // Add indexes to wishlists table
        Schema::table('wishlists', function (Blueprint $table) {
            // Composite index for user wishlist items
            $table->index(['user_id', 'agriculture_product_id'], 'idx_wishlist_user_product');
            
            // Index for product in wishlists
            $table->index('agriculture_product_id', 'idx_wishlist_product');
        });

        // Add indexes to dealer_registrations table
        Schema::table('dealer_registrations', function (Blueprint $table) {
            // Index for user's dealer registration
            $table->index('user_id', 'idx_dealer_reg_user');
            
            // Index for status filtering
            $table->index('status', 'idx_dealer_reg_status');
            
            // Index for approval tracking
            $table->index(['status', 'created_at'], 'idx_dealer_reg_status_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from agriculture_products table
        Schema::table('agriculture_products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_stock');
            $table->dropIndex('idx_products_category_active');
            $table->dropIndex('idx_products_brand');
            $table->dropIndex('idx_products_power_source');
            $table->dropIndex('idx_products_featured');
            $table->dropIndex('idx_products_created');
            $table->dropIndex('idx_products_price');
            $table->dropIndex('idx_products_sale_price');
            $table->dropIndex('idx_products_dealer_price');
        });

        // Drop indexes from agriculture_orders table
        Schema::table('agriculture_orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user_status');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created');
            $table->dropIndex('idx_orders_number');
        });

        // Drop indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_dealer');
            if (Schema::hasColumn('users', 'email') && !Schema::hasIndex('users', 'users_email_unique')) {
                $table->dropIndex('idx_users_email');
            }
        });

        // Drop indexes from notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_read');
            $table->dropIndex('idx_notifications_read');
            $table->dropIndex('idx_notifications_type');
            $table->dropIndex('idx_notifications_created');
        });

        // Drop indexes from agriculture_order_items table
        Schema::table('agriculture_order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order');
            $table->dropIndex('idx_order_items_product');
        });

        // Drop indexes from agriculture_categories table
        Schema::table('agriculture_categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_active');
            $table->dropIndex('idx_categories_sort');
        });

        // Drop indexes from wishlists table
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex('idx_wishlist_user_product');
            $table->dropIndex('idx_wishlist_product');
        });

        // Drop indexes from dealer_registrations table
        Schema::table('dealer_registrations', function (Blueprint $table) {
            $table->dropIndex('idx_dealer_reg_user');
            $table->dropIndex('idx_dealer_reg_status');
            $table->dropIndex('idx_dealer_reg_status_date');
        });
    }
};

















