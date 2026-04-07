<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@travelnice.vn'],
            [
                'name'              => 'Admin TravelNice',
                'phone'             => '0901234567',
                'password'          => Hash::make('password'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Khách hàng mẫu
        $users = [
            ['name' => 'Nguyễn Văn An',  'email' => 'an@gmail.com',    'phone' => '0912345678'],
            ['name' => 'Trần Thị Bình',  'email' => 'binh@gmail.com',  'phone' => '0923456789'],
            ['name' => 'Lê Văn Cường',   'email' => 'cuong@gmail.com', 'phone' => '0934567890'],
            ['name' => 'Phạm Thị Dung',  'email' => 'dung@gmail.com',  'phone' => '0945678901'],
            ['name' => 'Hoàng Văn Em',   'email' => 'em@gmail.com',    'phone' => '0956789012'],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    ...$userData,
                    'password'          => Hash::make('password'),
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('user');
        }
    }
}