<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateNewAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists with this email
        $adminExists = User::where('email', 'admin@nexus.com')->first();
        
        if ($adminExists) {
            $this->command->warn('Admin with email admin@nexus.com already exists. Updating...');
            $adminExists->update([
                'name' => 'Nexus Admin',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'phone' => '+919876543210',
            ]);
            $this->command->info('Admin credentials updated successfully!');
        } else {
            // Create New Admin User
            User::create([
                'name' => 'Nexus Admin',
                'email' => 'admin@nexus.com',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'phone' => '+919876543210',
            ]);
            $this->command->info('New admin user created successfully!');
        }
        
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('           ADMIN LOGIN CREDENTIALS');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Email:    admin@nexus.com');
        $this->command->info('Password: Admin@123');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('Admin Login URL: /admin/login');
    }
}


