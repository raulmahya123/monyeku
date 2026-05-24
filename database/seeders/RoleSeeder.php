<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        $owner = User::where('email', 'test@example.com')->first();
        if ($owner && !$company->users()->where('user_id', $owner->id)->exists()) {
            $company->users()->attach($owner->id, ['role' => 'owner', 'is_active' => true]);
        }
    }
}
