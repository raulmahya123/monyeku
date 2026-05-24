<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update a default admin user with known credentials
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create or find a default company and attach the admin as owner (idempotent)
        $company = Company::firstOrCreate([
            'slug' => 'contoh-company',
        ], [
            'name' => 'Contoh Company',
            'address' => 'Jl. Contoh No.1',
            'phone' => '081234567890',
            'email' => 'info@contoh.test',
            'is_active' => true,
        ]);

        $company->users()->syncWithoutDetaching([
            $admin->id => [
                'role' => 'owner',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $admin->current_company_id = $company->id;
        $admin->save();

        // Categories are created automatically when a Company is created.
    }
}