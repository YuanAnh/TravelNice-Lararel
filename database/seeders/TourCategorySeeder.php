<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourCategory;

class TourCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tour trong nước',  'slug' => 'tour-trong-nuoc',  'description' => 'Các tour du lịch trong nước Việt Nam'],
            ['name' => 'Tour nước ngoài',  'slug' => 'tour-nuoc-ngoai',  'description' => 'Các tour du lịch nước ngoài'],
            ['name' => 'Tour mùa hoa',     'slug' => 'tour-mua-hoa',     'description' => 'Tour ngắm hoa theo mùa'],
            ['name' => 'Combo du lịch',    'slug' => 'combo-du-lich',    'description' => 'Combo tour + khách sạn + vé máy bay'],
            ['name' => 'Tour nghỉ dưỡng',  'slug' => 'tour-nghi-duong',  'description' => 'Tour nghỉ dưỡng cao cấp'],
            ['name' => 'Tour khám phá',    'slug' => 'tour-kham-pha',    'description' => 'Tour khám phá thiên nhiên, văn hóa'],
        ];

        foreach ($categories as $cat) {
            TourCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}