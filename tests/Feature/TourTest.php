<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourTest extends TestCase
{
    use RefreshDatabase;

    public function test_tour_index_page_loads_successfully(): void
    {
        $response = $this->get('/tours');
        $response->assertStatus(200);
        $response->assertViewIs('tours.index');
    }

    public function test_tour_index_returns_active_tours_only(): void
    {
        $destination = Destination::factory()->create();
        $category    = TourCategory::factory()->create();

        Tour::factory()->create([
            'status'         => 'active',
            'destination_id' => $destination->id,
            'category_id'    => $category->id,
        ]);
        Tour::factory()->create([
            'status'         => 'inactive',
            'destination_id' => $destination->id,
            'category_id'    => $category->id,
        ]);

        $response = $this->get('/tours');
        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('tours')->total());
    }

    public function test_tour_search_by_keyword(): void
    {
        $destination = Destination::factory()->create();
        $category    = TourCategory::factory()->create();

        Tour::factory()->create([
            'title'          => 'Tour Nhật Bản Mùa Hoa',
            'status'         => 'active',
            'destination_id' => $destination->id,
            'category_id'    => $category->id,
        ]);
        Tour::factory()->create([
            'title'          => 'Tour Hàn Quốc Mùa Thu',
            'status'         => 'active',
            'destination_id' => $destination->id,
            'category_id'    => $category->id,
        ]);

        $response = $this->get('/tours?q=Nhật Bản');
        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('tours')->total());
    }

    public function test_tour_detail_page_loads_for_active_tour(): void
    {
        $destination = Destination::factory()->create();
        $category    = TourCategory::factory()->create();
        $tour = Tour::factory()->create([
            'status'         => 'active',
            'destination_id' => $destination->id,
            'category_id'    => $category->id,
        ]);

        $response = $this->get("/tours/{$tour->slug}");
        $response->assertStatus(200);
        $response->assertViewIs('tours.show');
        $response->assertSee($tour->title);
    }

    public function test_tour_sort_by_price_asc(): void
    {
        $destination = Destination::factory()->create();
        $category    = TourCategory::factory()->create();

        Tour::factory()->create(['price_adult' => 10000000, 'status' => 'active', 'destination_id' => $destination->id, 'category_id' => $category->id]);
        Tour::factory()->create(['price_adult' => 5000000,  'status' => 'active', 'destination_id' => $destination->id, 'category_id' => $category->id]);

        $response = $this->get('/tours?sort=price_asc');
        $response->assertStatus(200);

        $tours = $response->viewData('tours')->items();
        $this->assertLessThanOrEqual($tours[1]->price_adult, $tours[0]->price_adult ?? PHP_INT_MAX);
    }

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('home');
    }
}