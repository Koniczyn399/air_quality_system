<?php

namespace Database\Seeders;

use App\Enums\Auth\RoleType;
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

        User::factory(25)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('12345678'),
        ])->assignRole(RoleType::USER->value);

        User::factory()->create([
            'name' => 'Mainteiner User',
            'email' => 'mainteiner@example.com',
            'password' => Hash::make('12345678'),
        ])->assignRole(RoleType::MAINTEINER->value);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
        ])->assignRole(RoleType::ADMIN->value);
    }
}
