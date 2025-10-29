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
        Schema::create('dealer_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            
            // Business Information
            $table->string('business_name');
            $table->string('gst_number')->unique();
            $table->string('pan_number');
            $table->text('business_address');
            $table->string('business_city');
            $table->string('business_state');
            $table->string('business_pincode');
            $table->string('business_country')->default('India');
            
            // Contact Information
            $table->string('contact_person');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('alternate_phone')->nullable();
            $table->string('company_website')->nullable();
            
            // Business Details
            $table->text('business_description');
            $table->string('business_type'); // Individual, Partnership, Company, etc.
            $table->integer('years_in_business')->nullable();
            $table->string('annual_turnover')->nullable();
            $table->json('business_documents')->nullable(); // Store document paths
            
            // Registration Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Additional Information
            $table->json('additional_info')->nullable(); // For future extensibility
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('gst_number');
            $table->index('pan_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_registrations');
    }
};