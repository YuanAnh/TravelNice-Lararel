<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\TourSlot;
use App\Models\TourSchedule;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $tours = [
            [
                'destination_id' => 1, // Hàn Quốc
                'category_id'    => 3, // Tour mùa hoa
                'title'          => 'Hàn Quốc Mùa Hoa Anh Đào: HCM - Seoul - Đảo Nami - Rừng Seoul 5N4Đ',
                'slug'           => 'han-quoc-mua-hoa-anh-dao-hcm-seoul-5n4d',
                'description'    => 'Khám phá Hàn Quốc mùa hoa anh đào nở rộ, thăm cung điện Gyeongbokgung, đảo Nami thơ mộng và công viên Everland.',
                'duration_days'  => 5,
                'price_adult'    => 14190000,
                'price_child'    => 11000000,
                'max_slots'      => 30,
                'status'         => 'active',
                'avg_rating'     => 4.8,
            ],
            [
                'destination_id' => 2, // Nhật Bản
                'category_id'    => 3, // Tour mùa hoa
                'title'          => 'Nhật Bản Mùa Hoa Anh Đào 6N5Đ: Osaka - Kyoto - Núi Phú Sĩ - Tokyo',
                'slug'           => 'nhat-ban-mua-hoa-anh-dao-osaka-tokyo-6n5d',
                'description'    => 'Hành trình Nhật Bản ngắm hoa anh đào, thăm Kyoto cổ kính, leo núi Phú Sĩ hùng vĩ và khám phá Tokyo hiện đại.',
                'duration_days'  => 6,
                'price_adult'    => 27600000,
                'price_child'    => 22000000,
                'max_slots'      => 25,
                'status'         => 'active',
                'avg_rating'     => 4.9,
            ],
            [
                'destination_id' => 3, // Trung Quốc
                'category_id'    => 2, // Tour nước ngoài
                'title'          => 'Du Xuân Trung Hoa: HCM - Thượng Hải - Bắc Kinh - Hàng Châu - Ô Tô 7N6Đ',
                'slug'           => 'du-xuan-trung-hoa-thuong-hai-bac-kinh-7n6d',
                'description'    => 'Khám phá vẻ đẹp Trung Hoa với Thượng Hải hiện đại, Bắc Kinh lịch sử và Hàng Châu thơ mộng.',
                'duration_days'  => 7,
                'price_adult'    => 23490000,
                'price_child'    => 19000000,
                'max_slots'      => 35,
                'status'         => 'active',
                'avg_rating'     => 4.7,
            ],
            [
                'destination_id' => 7, // Đà Nẵng
                'category_id'    => 1, // Tour trong nước
                'title'          => 'Đà Nẵng - Hội An - Bà Nà Hills 4N3Đ',
                'slug'           => 'da-nang-hoi-an-ba-na-hills-4n3d',
                'description'    => 'Khám phá thành phố biển Đà Nẵng, phố cổ Hội An và khu du lịch Bà Nà Hills nổi tiếng.',
                'duration_days'  => 4,
                'price_adult'    => 5990000,
                'price_child'    => 4500000,
                'max_slots'      => 40,
                'status'         => 'active',
                'avg_rating'     => 4.6,
            ],
            [
                'destination_id' => 9, // Phú Quốc
                'category_id'    => 5, // Tour nghỉ dưỡng
                'title'          => 'Phú Quốc - Đảo Ngọc Biển Xanh 3N2Đ',
                'slug'           => 'phu-quoc-dao-ngoc-bien-xanh-3n2d',
                'description'    => 'Nghỉ dưỡng tại đảo ngọc Phú Quốc với bãi biển xanh trong, lặn ngắm san hô và ẩm thực hải sản tươi ngon.',
                'duration_days'  => 3,
                'price_adult'    => 4290000,
                'price_child'    => 3200000,
                'max_slots'      => 45,
                'status'         => 'active',
                'avg_rating'     => 4.5,
            ],
            [
                'destination_id' => 1, // Hàn Quốc
                'category_id'    => 4, // Combo
                'title'          => 'Combo Hàn Quốc 4N3Đ: Ibis Ambassador 3 Sao + Vé Máy Bay',
                'slug'           => 'combo-han-quoc-4n3d-ibis-ambassador',
                'description'    => 'Combo tiết kiệm Hàn Quốc bao gồm vé máy bay khứ hồi và khách sạn 3 sao tại Seoul.',
                'duration_days'  => 4,
                'price_adult'    => 18500000,
                'price_child'    => 15000000,
                'max_slots'      => 20,
                'status'         => 'active',
                'avg_rating'     => 4.4,
            ],
        ];

        foreach ($tours as $tourData) {
            $tour = Tour::create($tourData);

            // Tạo lịch slot khởi hành
            $dates = [
                now()->addDays(10),
                now()->addDays(20),
                now()->addDays(30),
                now()->addDays(45),
            ];

            foreach ($dates as $date) {
                TourSlot::create([
                    'tour_id'        => $tour->id,
                    'departure_date' => $date->format('Y-m-d'),
                    'total_slots'    => $tour->max_slots,
                    'booked_slots'   => rand(0, (int)($tour->max_slots * 0.6)),
                    'status'         => 'open',
                ]);
            }

            // Tạo lịch trình mẫu
            for ($day = 1; $day <= $tour->duration_days; $day++) {
                TourSchedule::create([
                    'tour_id'       => $tour->id,
                    'day_number'    => $day,
                    'title'         => "Ngày $day: Khám phá điểm đến",
                    'description'   => "Chương trình tham quan ngày $day của tour.",
                    'meals'         => 'Sáng, Trưa, Tối',
                    'accommodation' => 'Khách sạn 3-4 sao',
                ]);
            }
        }
    }
}
