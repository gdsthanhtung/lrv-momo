<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        return view('payment');
    }

    public function checkout(Request $request)
    {
        $data = $request->all();
        dump($data);
        echo "Processing payment...";

        //===

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

        $orderId = time() . "";
        $orderInfo = "Thanh toán qua ATM MoMo - ".$orderId;
        $amount = "10000";
        $redirectUrl = "http://localhost:8000/payment-complete";
        $ipnUrl = "http://localhost:8000/payment-complete";
        $extraData = "";

        $requestId = time() . "";
        $requestType = "payWithATM";
        //$extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = array('partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature);
        $result = $this->execPostRequest($endpoint, json_encode($data));

        $jsonResult = json_decode($result, true);  // decode json

        dd($jsonResult);
        //Just a example, please check more in there

        return redirect()->to($jsonResult['payUrl']);
        //header('Location: ' . $jsonResult['payUrl']);
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    public function paymentComplete()
    {
        echo "Payment completed!";
    }
}
