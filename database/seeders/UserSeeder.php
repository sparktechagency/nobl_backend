<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create admin
        User::create([
            'name'              => 'John Doe',
            'email'             => 'admin@gmail.com',
            'role'              => 'ADMIN',
            'email_verified_at' => now(),
            'password'          => Hash::make('1234'),
        ]);

        // create user
        User::create([
            'name'              => 'Jane Doe',
            'email'             => 'user@gmail.com',
            'address'           => '123 Main St',
            'badge_number'      => '123456',
            'email_verified_at' => now(),
            'password'          => Hash::make('1234'),
        ]);
    }
}
