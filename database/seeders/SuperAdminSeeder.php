<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $existing = User::first();

        if ($existing) {
            $existing->update([
                'role'            => 'superadmin',
                'status'          => 'active',
                'approval_status' => 'approved',
                'approved_at'     => now(),
            ]);
            $this->command->info("✓ Promoted {$existing->email} to superadmin.");
        } else {
            User::create([
                'name'            => 'Super Admin',
                'email'           => 'admin@adminview.com',
                'password'        => Hash::make('password'),
                'role'            => 'superadmin',
                'status'          => 'active',
                'approval_status' => 'approved',
                'approved_at'     => now(),
            ]);
            $this->command->info('✓ Created superadmin: admin@adminview.com / password');
        }
    }
}