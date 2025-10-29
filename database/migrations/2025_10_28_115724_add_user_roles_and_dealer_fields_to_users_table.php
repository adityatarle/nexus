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
        Schema::table('users', function (Blueprint $table) {
            // User role system
            $table->enum('role', ['customer', 'dealer', 'admin'])->default('customer')->after('email');
            
            // Dealer-specific fields
            $table->string('business_name')->nullable()->after('role');
            $table->string('gst_number')->nullable()->after('business_name');
            $table->text('business_address')->nullable()->after('gst_number');
            $table->string('contact_person')->nullable()->after('business_address');
            $table->string('phone')->nullable()->after('contact_person');
            $table->string('alternate_phone')->nullable()->after('phone');
            
            // Dealer status and approval
            $table->boolean('is_dealer_approved')->default(false)->after('alternate_phone');
            $table->timestamp('dealer_approved_at')->nullable()->after('is_dealer_approved');
            $table->unsignedBigInteger('approved_by')->nullable()->after('dealer_approved_at');
            $table->text('dealer_rejection_reason')->nullable()->after('approved_by');
            
            // Additional fields
            $table->string('company_website')->nullable()->after('dealer_rejection_reason');
            $table->text('business_description')->nullable()->after('company_website');
            $table->string('pan_number')->nullable()->after('business_description');
            
            // Indexes
            $table->index(['role', 'is_dealer_approved']);
            $table->index('gst_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'business_name',
                'gst_number',
                'business_address',
                'contact_person',
                'phone',
                'alternate_phone',
                'is_dealer_approved',
                'dealer_approved_at',
                'approved_by',
                'dealer_rejection_reason',
                'company_website',
                'business_description',
                'pan_number'
            ]);
        });
    }
};