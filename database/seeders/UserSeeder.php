<?php

namespace Database\Seeders;

use App\Models\{Role, User};
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'email' => 'admin@gmail.com', // admin
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'phone_number' => '082314209321',
                'gender' => 'Laki-laki',
                'role_id' => Role::find(1)->id,
                'is_active' => true,
            ],
            [
                'name' => 'Wiwan',
                'slug' => 'wiwan',
                'email' => 'wiwan@gmail.com', // owner
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'phone_number' => '082223413245',
                'gender' => 'Laki-laki',
                'role_id' => Role::find(2)->id,
                'is_active' => true,
            ],
            [
                'name' => 'Mijub',
                'slug' => 'mijub',
                'email' => 'mijub@gmail.com', // driver
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'phone_number' => '082376309812',
                'gender' => 'Laki-laki',
                'role_id' => Role::find(3)->id,
                'is_active' => true,
            ],
            [
                'name' => 'Rundi',
                'slug' => 'rundi',
                'email' => 'rundi@gmail.com', // customer
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'phone_number' => '082245091295',
                'gender' => 'Laki-laki',
                'role_id' => Role::find(4)->id,
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::query()->create($user);
        }
    }
}
