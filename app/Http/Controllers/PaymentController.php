<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\VNPayService;
use App\Services\MoMoService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private VNPayService $vnpay,
        private MoMoService  $momo,
    ) {}

    /**
     * Trang chọn phương thức thanh toán
     */
    public function select(Booking $booking)
    {
        // Chỉ cho phép thanh toán đơn pending
        if (!$booking->isPending()) {
            return redirect()->route('profile.index')
                ->with('error', 'Đơn này không thể thanh toán.');
        }

        // Chỉ user sở hữu mới được thanh toán
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.select', compact('booking'));
    }

    /**
     * Redirect sang VNPay
     */
    public function payVNPay(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || !$booking->isPending()) {
            abort(403);
        }

        $url = $this->vnpay->createPaymentUrl($booking);
        return redirect($url);
    }

    /**
     * Redirect sang MoMo
     */
    public function payMoMo(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || !$booking->isPending()) {
            abort(403);
        }

        $url = $this->momo->createPaymentUrl($booking);

        if (!$url) {
            return redirect()->back()->with('error', 'Không thể kết nối MoMo. Vui lòng thử lại.');
        }

        return redirect($url);
    }

    /**
     * VNPay callback (return URL)
     */
    public function vnpayReturn(Request $request)
    {
        if (!$this->vnpay->verifyReturn($request)) {
            return view('payment.result', ['success' => false, 'message' => 'Chữ ký không hợp lệ!']);
        }

        $responseCode = $request->get('vnp_ResponseCode');
        $bookingCode  = $this->vnpay->getBookingCode($request->get('vnp_TxnRef'));
        $booking      = Booking::where('booking_code', $bookingCode)->first();

        if ($responseCode === '00' && $booking) {
            $this->markPaid($booking, 'vnpay', $request->get('vnp_TransactionNo'), $request->get('vnp_Amount') / 100);
            return view('payment.result', [
                'success' => true,
                'booking' => $booking,
                'message' => 'Thanh toán VNPay thành công!',
            ]);
        }

        return view('payment.result', [
            'success' => false,
            'booking' => $booking,
            'message' => 'Thanh toán thất bại! Mã lỗi: ' . $responseCode,
        ]);
    }

    /**
     * MoMo return URL
     */
    public function momoReturn(Request $request)
    {
        $data = $request->all();

        if (!$this->momo->verifyReturn($data)) {
            return view('payment.result', ['success' => false, 'message' => 'Chữ ký không hợp lệ!']);
        }

        $bookingCode = $this->momo->getBookingCode($data['orderId']);
        $booking     = Booking::where('booking_code', $bookingCode)->first();

        if ($data['resultCode'] === 0 && $booking) {
            $this->markPaid($booking, 'momo', $data['transId'], $data['amount']);
            return view('payment.result', [
                'success' => true,
                'booking' => $booking,
                'message' => 'Thanh toán MoMo thành công!',
            ]);
        }

        return view('payment.result', [
            'success' => false,
            'booking' => $booking,
            'message' => 'Thanh toán thất bại! ' . ($data['message'] ?? ''),
        ]);
    }

    /**
     * MoMo IPN (server-to-server notify)
     */
    public function momoNotify(Request $request)
    {
        $data = $request->all();

        if (!$this->momo->verifyReturn($data)) {
            return response()->json(['message' => 'invalid signature'], 400);
        }

        if ($data['resultCode'] === 0) {
            $bookingCode = $this->momo->getBookingCode($data['orderId']);
            $booking     = Booking::where('booking_code', $bookingCode)->first();
            if ($booking && $booking->isPending()) {
                $this->markPaid($booking, 'momo', $data['transId'], $data['amount']);
            }
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * Đánh dấu booking đã thanh toán và tạo payment record
     */
    private function markPaid(Booking $booking, string $gateway, string $transactionId, float $amount): void
    {
        if (!$booking->isPending()) return;

        $booking->update(['status' => 'paid']);

        Payment::create([
            'booking_id'     => $booking->id,
            'gateway'        => $gateway,
            'transaction_id' => $transactionId,
            'amount'         => $amount,
            'status'         => 'success',
            'paid_at'        => now(),
        ]);
    }
}