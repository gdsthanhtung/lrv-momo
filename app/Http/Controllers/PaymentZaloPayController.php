<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentZaloPayController extends Controller
{
    protected $appId                = 2554;
    protected $key1                 = 'sdngKKJmqEMzvh5QQcdD2A9XBSKUNaYn';
    protected $key2                 = 'trMrHtvjo6myautxDUiAcYsVtaeQ8nhf';

    protected $endpointCheckout     = 'https://sb-openapi.zalopay.vn/v2/create';
    protected $endpointCheckStatus  = 'https://sb-openapi.zalopay.vn/v2/query';

    protected $redirectUrl          = "https://f9b5-14-186-211-79.ngrok-free.app/payment-callback-zalopay";

    public function checkout(Request $request)
    {
        //Doc - https://docs.zalopay.vn/v2/general/overview.html#tao-don-hang

        $data = $request->all();
        $config = [
            "app_id" => $this->appId,
            "key1" => $this->key1,
            "key2" => $this->key2,
            "endpoint" => $this->endpointCheckout
        ];

        $embeddata = json_encode(['redirectUrl' => $this->redirectUrl]); // Merchant's data
        $items = '[{"itemid":"knb","itemname":"kim nguyen bao","itemprice":198400,"itemquantity":1}]'; // Merchant's data
        $transID = rand(0,1000000); //Random trans id
        $order = [
            "app_id" => $config["app_id"],
            "app_time" => round(microtime(true) * 1000), // miliseconds
            "app_trans_id" => date("ymd") . "_" . $transID, // translation missing: vi.docs.shared.sample_code.comments.app_trans_id
            "app_user" => "user123",
            "item" => $items,
            "embed_data" => $embeddata,
            "amount" => $data["amount"],
            "description" => "Lazada - Payment for the order #$transID",
            //"bank_code" => "zalopayapp" // Mở web zalopay with QR Code
            "bank_code" => "" // Mở web zalopay và chọn phương thức thanh toán
        ];

        // appid|app_trans_id|appuser|amount|apptime|embeddata|item
        $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"]
            . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];
        $order["mac"] = hash_hmac("sha256", $data, $config["key1"]);

        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($order)
            ]
        ]);

        $result = file_get_contents($config["endpoint"], false, $context);
        $jsonResult = json_decode($result, true);

        dump($jsonResult);
        if (isset($jsonResult['return_code']) && $jsonResult['return_code'] == 1){
            Log::channel('zalopay_payment')->info('Create transaction | Info: ' . json_encode($result));
            dump("Redirecting to ZaloPay! Please wait...");
            echo "<script>
                    setTimeout(function() {
                    window.open('".$jsonResult['order_url']."', '_blank');
                    }, 5000);
                </script>";
        }else{
            Log::channel('zalopay_payment')->error('Create transaction | Error: ' . json_encode($result));
            dd($jsonResult);
        }
    }

    public function callback(Request $request)
    {
        echo "Payment zalopay completed!";
        $data = $request->all();
        Log::channel('zalopay_payment')->info('Zalopay callback: ' . json_encode($data));
        dd($data);
    }

    public function checkStatus(Request $request)
    {
        //Doc - https://docs.zalopay.vn/v2/general/overview.html#truy-van-trang-thai-thanh-toan-cua-don-hang
        //Test - http://localhost:80

        $data = $request->all();
        $config = [
            "app_id" => $this->appId,
            "key1" => $this->key1,
            "key2" => $this->key2,
            "endpoint" => $this->endpointCheckStatus
        ];

        $app_trans_id = $data['app_trans_id'];  // Input your app_trans_id
        $data = $config["app_id"]."|".$app_trans_id."|".$config["key1"]; // app_id|app_trans_id|key1
        $params = [
            "app_id" => $config["app_id"],
            "app_trans_id" => $app_trans_id,
            "mac" => hash_hmac("sha256", $data, $config["key1"])
        ];

        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($params)
            ]
        ]);

        $result = file_get_contents($config["endpoint"], false, $context);
        $jsonResult = json_decode($result, true);

        echo "Check zalopay transaction status!";
        $data = $request->all();
        Log::channel('zalopay_payment')->info('Check payment status: ' . json_encode($result));
        dd($jsonResult);
    }
}
