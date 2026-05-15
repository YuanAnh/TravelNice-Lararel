<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class, // ← phải chạy TRƯỚC để role tồn tại
            UserSeeder::class,           // ← sau đó mới gán role cho user
            DestinationSeeder::class,
            TourCategorySeeder::class,
            TourSeeder::class,
        ]);
    }
}