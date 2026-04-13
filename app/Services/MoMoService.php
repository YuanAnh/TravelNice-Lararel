<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Str;

class MoMoService
{
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $endpoint;
    private string $returnUrl;
    private string $notifyUrl;

    public function __construct()
    {
        $this->partnerCode = config('payment.momo.partner_code');
        $this->accessKey   = config('payment.momo.access_key');
        $this->secretKey   = config('payment.momo.secret_key');
        $this->endpoint    = config('payment.momo.endpoint');
        $this->returnUrl   = config('payment.momo.return_url');
        $this->notifyUrl   = config('payment.momo.notify_url');
    }

    /**
     * Tạo request thanh toán MoMo, trả về payUrl
     */
    public function createPaymentUrl(Booking $booking): ?string
    {
        $orderId    = $booking->booking_code . '_' . time();
        $requestId  = Str::uuid()->toString();
        $amount     = (int) $booking->total_price;
        $orderInfo  = 'Thanh toan booking ' . $booking->booking_code;
        $requestType = config('payment.momo.request_type');
        $extraData  = base64_encode(json_encode(['booking_code' => $booking->booking_code]));

        $rawHash = "accessKey={$this->accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$this->notifyUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$this->partnerCode}"
            . "&redirectUrl={$this->returnUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $body = [
            'partnerCode' => $this->partnerCode,
            'accessKey'   => $this->accessKey,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $this->returnUrl,
            'ipnUrl'      => $this->notifyUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        $response = $this->callApi($body);

        if ($response && $response['resultCode'] === 0) {
            return $response['payUrl'];
        }

        return null;
    }

    /**
     * Xác thực callback từ MoMo
     */
    public function verifyReturn(array $data): bool
    {
        $rawHash = "accessKey={$this->accessKey}"
            . "&amount={$data['amount']}"
            . "&extraData={$data['extraData']}"
            . "&message={$data['message']}"
            . "&orderId={$data['orderId']}"
            . "&orderInfo={$data['orderInfo']}"
            . "&orderType={$data['orderType']}"
            . "&partnerCode={$data['partnerCode']}"
            . "&payType={$data['payType']}"
            . "&requestId={$data['requestId']}"
            . "&responseTime={$data['responseTime']}"
            . "&resultCode={$data['resultCode']}"
            . "&transId={$data['transId']}";

        $expected = hash_hmac('sha256', $rawHash, $this->secretKey);
        return hash_equals($expected, $data['signature'] ?? '');
    }

    public function getBookingCode(string $orderId): string
    {
        return explode('_', $orderId)[0];
    }

    private function callApi(array $body): ?array
    {
        try {
            $ch = curl_init($this->endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($body),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            return json_decode($result, true);
        } catch (\Exception $e) {
            \Log::error('MoMo API error: ' . $e->getMessage());
            return null;
        }
    }
}