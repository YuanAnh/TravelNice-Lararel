<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Mail\NewBookingPaid;
use App\Services\VNPayService;
use App\Services\MoMoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct(
        private VNPayService $vnpay,
        private MoMoService  $momo,
    ) {}

    public function select(Booking $booking)
    {
        if (!$booking->isPending()) {
            return redirect()->route('profile.index')->with('error', 'Đơn này không thể thanh toán.');
        }
        if ($booking->user_id !== auth()->id()) abort(403);
        return view('payment.select', compact('booking'));
    }

    public function payVNPay(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || !$booking->isPending()) abort(403);
        $url = $this->vnpay->createPaymentUrl($booking);
        return redirect($url);
    }

    public function payMoMo(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || !$booking->isPending()) abort(403);
        $url = $this->momo->createPaymentUrl($booking);
        if (!$url) {
            return redirect()->back()->with('error', 'Không thể kết nối MoMo. Vui lòng thử lại.');
        }
        return redirect($url);
    }

    public function vnpayReturn(Request $request)
    {
        // Gọi thẳng vào Service, Service sẽ lo hết từ xác thực đến lưu Database
        $result = $this->vnpay->handleReturn($request->all());

        // Nếu thành công, Service đã load sẵn model $booking, ta chỉ việc bóc ra gửi email
        if ($result['success'] && isset($result['booking'])) {
            if ($result['booking']->wasChanged('status')) {
                $this->notifyAdmins($result['booking']);
                }
        }

        return view('payment.result', [
            'success' => $result['success'],
            'booking' => $result['booking'] ?? null,
            'message' => $result['message']
        ]);
    }

    /**
     * Luồng 2: Máy chủ VNPay gọi ngầm để xác nhận (IPN / Webhook)
     */
    public function vnpayIpn(Request $request)
    {
        // Trả về chuỗi JSON với RspCode chuẩn xác mà máy chủ VNPay yêu cầu
        $response = $this->vnpay->handleIpn($request->all());
        
        // Nếu giao dịch thành công (00), ta gọi luồng gửi mail
        if ($response['RspCode'] === '00') {
            $bookingCode = $this->vnpay->getBookingCode($request->get('vnp_TxnRef'));
            $booking = Booking::with(['user', 'tourSlot.tour', 'payment'])->where('booking_code', $bookingCode)->first();
            if ($booking) {
                $this->notifyAdmins($booking);
            }
        }

        return response()->json($response);
    }

    public function momoReturn(Request $request)
    {
        $data    = $request->all();
        if (!$this->momo->verifyReturn($data)) {
            return view('payment.result', ['success' => false, 'message' => 'Chữ ký không hợp lệ!']);
        }

        $bookingCode = $this->momo->getBookingCode($data['orderId']);
        $booking     = Booking::with(['user','tourSlot.tour','payment'])->where('booking_code', $bookingCode)->first();

        if ((int)$data['resultCode'] === 0 && $booking && $booking->isPending()) {
            $this->markPaid($booking, 'momo', $data['transId'], $data['amount']);
            return view('payment.result', ['success' => true, 'booking' => $booking, 'message' => 'Thanh toán MoMo thành công!']);
        }

        return view('payment.result', ['success' => false, 'booking' => $booking, 'message' => 'Thanh toán thất bại! ' . ($data['message'] ?? '')]);
    }

    public function momoNotify(Request $request)
    {
        $data = $request->all();
        if (!$this->momo->verifyReturn($data)) {
            return response()->json(['message' => 'invalid signature'], 400);
        }
        if ((int)$data['resultCode'] === 0) {
            $bookingCode = $this->momo->getBookingCode($data['orderId']);
            $booking     = Booking::with(['user','tourSlot.tour','payment'])->where('booking_code', $bookingCode)->first();
            if ($booking && $booking->isPending()) {
                $this->markPaid($booking, 'momo', $data['transId'], $data['amount']);
            }
        }
        return response()->json(['message' => 'ok']);
    }

    private function markPaid(Booking $booking, string $gateway, string $transactionId, float $amount): void
    {
        if (!$booking->isPending()) return;

        $booking->update(['status' => 'paid']);

        Payment::create([
            'booking_id'     => $booking->id,
            'gateway'        => $gateway,
            'transaction_id' => $transactionId,
            'amount'         => $booking->netTotal(),
            'status'         => 'success',
            'paid_at'        => now(),
        ]);

        // Reload relationships
        $booking->load(['user', 'tourSlot.tour', 'payment']);

        // Gửi email thông báo cho tất cả admin
        $this->notifyAdmins($booking);
    }

    private function notifyAdmins(Booking $booking): void
    {
        try {
            $primaryAdminEmail = env('MAIL_ADMIN', 'admin@travelnice.vn');

            Mail::to($primaryAdminEmail)->queue(new NewBookingPaid($booking));

            User::role('admin')->each(function ($admin) use ($booking, $primaryAdminEmail) {
                if ($admin->email !== $primaryAdminEmail) {
                    Mail::to($admin->email)->queue(new NewBookingPaid($booking));
                }
            });

        } catch (\Exception $e) {
            \Log::error('Failed to send admin notification: ' . $e->getMessage());
        }
    }
}