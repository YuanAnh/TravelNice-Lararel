<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            // Nước ngoài
            ['name' => 'Hàn Quốc',    'region' => 'Đông Bắc Á',  'country' => 'Hàn Quốc'],
            ['name' => 'Nhật Bản',    'region' => 'Đông Bắc Á',  'country' => 'Nhật Bản'],
            ['name' => 'Trung Quốc',  'region' => 'Đông Bắc Á',  'country' => 'Trung Quốc'],
            ['name' => 'Singapore',   'region' => 'Đông Nam Á',  'country' => 'Singapore'],
            ['name' => 'Thái Lan',    'region' => 'Đông Nam Á',  'country' => 'Thái Lan'],
            ['name' => 'Bali',        'region' => 'Đông Nam Á',  'country' => 'Indonesia'],
            // Trong nước
            ['name' => 'Đà Nẵng',    'region' => 'Miền Trung',  'country' => 'Việt Nam'],
            ['name' => 'Nha Trang',   'region' => 'Miền Trung',  'country' => 'Việt Nam'],
            ['name' => 'Phú Quốc',   'region' => 'Miền Nam',    'country' => 'Việt Nam'],
            ['name' => 'Hạ Long',    'region' => 'Miền Bắc',    'country' => 'Việt Nam'],
            ['name' => 'Sapa',        'region' => 'Miền Bắc',    'country' => 'Việt Nam'],
            ['name' => 'Đà Lạt',     'region' => 'Tây Nguyên',  'country' => 'Việt Nam'],
        ];

        foreach ($destinations as $dest) {
            Destination::create($dest);
        }
    }
}
