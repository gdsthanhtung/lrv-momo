<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentMomoController extends Controller
{
    protected $accessKey            = 'F8BBA842ECF85';
    protected $secretKey            = 'K951B6PE1waDMi640xX08PD3vg6EkVlz';

    protected $endpointCheckout     = 'https://test-payment.momo.vn/v2/gateway/api/create';
    protected $endpointCheckStatus  = 'https://test-payment.momo.vn/v2/gateway/api/query';

    protected $redirectUrl          = "https://f9b5-14-186-211-79.ngrok-free.app/payment-complete-momo";
    protected $ipnUrl               = "https://f9b5-14-186-211-79.ngrok-free.app/payment-callback-momo";

    protected $partnerCode          = 'MOMO';
    protected $lang                 = 'vi';

    public function showPaymentForm()
    {
        return view('payment');
    }

    public function checkout(Request $request)
    {
        //Doc - https://developers.momo.vn/v3/vi/docs/payment/api/collection-link

        $data = $request->all();
        $orderDetail = [
            "phone" => "0987654321",
            "productId" => [111,222],
            "productName" => ["product 1", "product 2"],
            "quantity" => [1,2],
            "amount" => [100000, 200000],
            "discount" => [0, 0],
            "fee" => [0, 0],
            "vat" => [0, 0],
            "totalAmount" => [100000, 200000],
            "grandTotal" => 300000
        ];

        $endpoint       = $this->endpointCheckout;
        $redirectUrl    = $this->redirectUrl;
        $ipnUrl         = $this->ipnUrl;
        $accessKey      = $this->accessKey;
        $secretKey      = $this->secretKey;
        $partnerCode    = $this->partnerCode;
        $amount         = $request->amount;
        $orderId        = time() . "";
        $requestId      = time() . "";
        $extraData      = base64_encode(json_encode($orderDetail));

        $orderInfo      = 'pay with MoMo';
        $requestType    = 'payWithMethod';
        $partnerName    = 'MoMo Payment';
        $storeId        = 'Test Store';
        $orderGroupId   = '';
        $autoCapture    = True;
        $lang           = $this->lang;

        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => $partnerName,
            'storeId' => $storeId,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'requestType' => $requestType,
            'ipnUrl' => $ipnUrl,
            'lang' => $lang,
            'redirectUrl' => $redirectUrl,
            'autoCapture' => $autoCapture,
            'extraData' => $extraData,
            'orderGroupId' => $orderGroupId,
            'signature' => $signature);

        $result     = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        if (isset($jsonResult['payUrl']) && $jsonResult['payUrl'] != null){
            Log::channel('momo_payment')->info('Create transaction | Info: ' . json_encode($result));
            return redirect()->to($jsonResult['payUrl']);
        }else{
            Log::channel('momo_payment')->error('Create transaction | Error: ' . json_encode($result));
            dd($jsonResult);
        }
    }

    public function callback(Request $request)
    {
        echo "Payment momo completed!";
        $data = $request->all();
        Log::channel('momo_payment')->info('MoMo callback: ' . json_encode($data));
        dd($data);
    }

    public function checkStatus(Request $request)
    {
        //Doc - https://developers.momo.vn/v3/vi/docs/payment/api/payment-api/query
        //Test - http://localhost:8000/payment-check-status-momo?orderId=1730186470

        $orderId        = $request->orderId;
        $endpoint       = $this->endpointCheckStatus;
        $accessKey      = $this->accessKey;
        $secretKey      = $this->secretKey;

        $requestId      = time() . "";
        $lang           = $this->lang;
        $partnerCode    = $this->partnerCode;
        $rawHash        = "accessKey=" . $accessKey . "&orderId=" . $orderId . "&partnerCode=" . $partnerCode . "&requestId=" . $requestId;
        $signature      = hash_hmac("sha256", $rawHash, $secretKey);

        $data = array(
            'partnerCode'   => $partnerCode,
            'requestId'     => $requestId,
            'orderId'       => $orderId,
            'signature'     => $signature,
            'lang'          => $lang
        );

        $result     = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        echo "Check Momo transaction status!";
        $data = $request->all();
        Log::channel('momo_payment')->info('Check payment status: ' . json_encode($result));
        dd($jsonResult);
    }

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //Seconds
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //Seconds
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
