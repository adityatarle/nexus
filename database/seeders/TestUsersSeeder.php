<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DealerRegistration;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@nexus.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        echo "✓ Admin created: admin@nexus.com / admin123\n";

        // 2. Customer User
        $customer = User::firstOrCreate(
            ['email' => 'customer@nexus.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );
        echo "✓ Customer created: customer@nexus.com / customer123\n";

        // 3. Approved Dealer User
        $dealer = User::firstOrCreate(
            ['email' => 'dealer@nexus.com'],
            [
                'name' => 'Test Dealer',
                'password' => Hash::make('dealer123'),
                'role' => 'dealer',
                'is_dealer_approved' => true,
                'dealer_approved_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        
        // Create dealer registration if not exists
        if ($dealer) {
            DealerRegistration::firstOrCreate(
                ['user_id' => $dealer->id],
                [
                    'business_name' => 'Test Dealer Business',
                    'business_type' => 'Wholesaler',
                    'gst_number' => 'GST123456789',
                    'pan_number' => 'ABCDE1234F',
                    'business_address' => '123 Business Street',
                    'business_city' => 'Mumbai',
                    'business_state' => 'Maharashtra',
                    'business_pincode' => '400001',
                    'business_country' => 'India',
                    'contact_person' => 'Test Dealer',
                    'contact_email' => 'dealer@nexus.com',
                    'contact_phone' => '9876543210',
                    'alternate_phone' => '9876543211',
                    'company_website' => 'https://testdealer.com',
                    'business_description' => 'Test dealer business for agricultural products',
                    'years_in_business' => 5,
                    'terms_accepted' => true,
                    'terms_accepted_at' => now(),
                    'status' => 'approved',
                    'reviewed_at' => now(),
                    'reviewed_by' => $admin->id,
                ]
            );
        }
        echo "✓ Approved Dealer created: dealer@nexus.com / dealer123\n";

        // 4. Pending Dealer User
        $pendingDealer = User::firstOrCreate(
            ['email' => 'pending@nexus.com'],
            [
                'name' => 'Pending Dealer',
                'password' => Hash::make('pending123'),
                'role' => 'dealer',
                'is_dealer_approved' => false,
                'email_verified_at' => now(),
            ]
        );
        
        // Create dealer registration if not exists
        if ($pendingDealer) {
            DealerRegistration::firstOrCreate(
                ['user_id' => $pendingDealer->id],
                [
                    'business_name' => 'Pending Dealer Business',
                    'business_type' => 'Retailer',
                    'gst_number' => 'GST987654321',
                    'pan_number' => 'FGHIJ5678K',
                    'business_address' => '456 Pending Lane',
                    'business_city' => 'Delhi',
                    'business_state' => 'Delhi',
                    'business_pincode' => '110001',
                    'business_country' => 'India',
                    'contact_person' => 'Pending Dealer',
                    'contact_email' => 'pending@nexus.com',
                    'contact_phone' => '9876543220',
                    'alternate_phone' => null,
                    'company_website' => null,
                    'business_description' => 'Pending dealer application for agricultural products',
                    'years_in_business' => 2,
                    'terms_accepted' => true,
                    'terms_accepted_at' => now(),
                    'status' => 'pending',
                ]
            );
        }
        echo "✓ Pending Dealer created: pending@nexus.com / pending123\n";

        echo "\n=== TEST CREDENTIALS ===\n";
        echo "Admin Login:\n";
        echo "  URL: http://127.0.0.1:8000/admin/login\n";
        echo "  Email: admin@nexus.com\n";
        echo "  Password: admin123\n\n";

        echo "Customer Login:\n";
        echo "  URL: http://127.0.0.1:8000/auth/customer-login\n";
        echo "  Email: customer@nexus.com\n";
        echo "  Password: customer123\n\n";

        echo "Approved Dealer Login:\n";
        echo "  URL: http://127.0.0.1:8000/auth/dealer-login\n";
        echo "  Email: dealer@nexus.com\n";
        echo "  Password: dealer123\n\n";

        echo "Pending Dealer Login:\n";
        echo "  URL: http://127.0.0.1:8000/auth/dealer-login\n";
        echo "  Email: pending@nexus.com\n";
        echo "  Password: pending123\n";
        echo "========================\n";
    }
}

