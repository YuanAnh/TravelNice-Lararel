<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TourSlot;
use App\Models\Tour;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tour_id'      => 'required|exists:tours,id',
            'slot_id'      => 'required|exists:tour_slots,id',
            'num_adults'   => 'required|integer|min:1|max:50',
            'num_children' => 'nullable|integer|min:0|max:50',
            'note'         => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // 1. Lock slot — FIX: gộp thành 1 query duy nhất
                $slot = TourSlot::where('id', $request->slot_id)
                                ->lockForUpdate()
                                ->firstOrFail();

                // 2. Kiểm tra slot còn mở — FIX: thêm guard này
                if ($slot->status !== 'open') {
                    throw new \Exception('Suất khởi hành này đã đóng hoặc hết chỗ.');
                }

                // 3. Kiểm tra tour còn active — FIX: thêm guard này
                $tour = Tour::where('id', $request->tour_id)
                            ->where('status', 'active')
                            ->firstOrFail();

                $adults    = (int) $request->num_adults;
                $children  = (int) ($request->num_children ?? 0);
                $requested = $adults + $children;

                // 4. Kiểm tra đủ chỗ — trả về số cụ thể
                if ($slot->remainingSlots() < $requested) {
                    throw new \Exception(
                        "Chỉ còn {$slot->remainingSlots()} chỗ trống, không đủ cho {$requested} người."
                    );
                }

                // 5. Tính tiền — FIX: price_child nullable dùng ?? 0
                $total = ($adults * $tour->price_adult)
                       + ($children * ($tour->price_child ?? 0));

                // 6. Tạo booking
                $booking = Booking::create([
                    'user_id'      => auth()->id(),
                    'tour_slot_id' => $slot->id,
                    'num_adults'   => $adults,
                    'num_children' => $children,
                    'total_price'  => $total,
                    'note'         => $request->note,
                    'status'       => 'pending',
                ]);

                // 6b. Ghi nhận hành vi đặt tour cho AI behavior engine
                if (Auth::check()) {
                    app(GeminiService::class)->trackBooking(
                        Auth::id(),
                        $tour->id,
                    );
                }

                // 7. Tăng booked_slots
                $slot->increment('booked_slots', $requested);

                // 8. FIX: dùng fresh() để lấy giá trị mới nhất sau increment
                if ($slot->fresh()->remainingSlots() <= 0) {
                    $slot->update(['status' => 'full']);
                }
            });

            return redirect()->back()->with(
                'success',
                'Đặt tour thành công! Chúng tôi sẽ liên hệ xác nhận trong 24h.'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return redirect()->back()->with('error', 'Tour hoặc suất khởi hành không còn hoạt động.');

        } catch (\Exception $e) {
            // FIX: log để debug, không lộ thông tin nhạy cảm ra ngoài
            Log::warning('Booking failed', [
                'user_id' => auth()->id(),
                'tour_id' => $request->tour_id,
                'slot_id' => $request->slot_id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}