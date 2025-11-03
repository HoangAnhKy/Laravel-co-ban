- [chuẩn bị môi trường](#chuẩn-bị-môi-trường)
- [route](#route)
- [layout test](#layout-test)
- [code trong controller](#code-trong-controller)
- [phân tích và làm rõ code backend](#phân-tích-và-làm-rõ-code-backend)
  - [đoạn chính endpoind momo](#đoạn-chính-endpoind-momo)
  - [đoạn thông tin order](#đoạn-thông-tin-order)
  - [Tạo chuỗi ký và mã hóa (chống giả mạo)](#tạo-chuỗi-ký-và-mã-hóa-chống-giả-mạo)
  - [Gửi request đến MoMo API](#gửi-request-đến-momo-api)
***
# chuẩn bị môi trường

```sh 
# demo 
MOMO_PARTNER_CODE=MOMO
MOMO_ACCESS_KEY=F8BBA842ECF85
MOMO_SECRET_KEY=K951B6PE1waDMi640xX08PD3vg6EkVlz
MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create
MOMO_RETURN_URL=https://draftier-fernanda-unrecognisably.ngrok-free.dev/payment/return
MOMO_NOTIFY_URL=https://draftier-fernanda-unrecognisably.ngrok-free.dev/payment/notify
```

# route

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('test-momo');
});
Route::post('/payment/create', [App\Http\Controllers\PaymentController::class, 'createPayment'])->name("payment.create");
Route::get('/payment/return', [App\Http\Controllers\PaymentController::class, 'return']);
Route::post('/payment/notify', [App\Http\Controllers\PaymentController::class, 'notify']);
```

# layout test

```html
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test MoMo Payment</title>
</head>
<body>
<h1>Test MoMo Payment</h1>
<form action="{{route('payment.create')}}" method="POST">
    @csrf
    <label>Số tiền:</label>
    <input type="number" name="amount" value="10000"><br><br>
    <button type="submit">Thanh toán MoMo</button>
</form>
</body>
</html>
```

# code trong controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function momoPayment(Request $request)
    {
        $endpoint = env('MOMO_ENDPOINT');
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');

        $orderId = time();
        $orderInfo = "Thanh toán demo MoMo";
        $amount = "10000"; // 10,000 VND
        $redirectUrl = env('MOMO_RETURN_URL');
        $ipnUrl = env('MOMO_NOTIFY_URL');
        $requestId = time();
        $requestType = "captureWallet";
        $extraData = "";

        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        $response = Http::post($endpoint, $data);

        $result = $response->json();

        if (isset($result['payUrl'])) {
            return redirect($result['payUrl']);
        } else {
            return response()->json($result);
        }
    }

    // tự code giao diện thanh toán
    public function createPayment(Request $request)
    {
        $endpoint = env('MOMO_ENDPOINT');
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');
        $orderId = time(); // mã đơn hàng tạm
        $amount = $request->amount ?? 10000; // test 10k
        $orderInfo = "Thanh toán đơn hàng #$orderId";
        $redirectUrl = env('MOMO_RETURN_URL');
        $ipnUrl = env('MOMO_NOTIFY_URL');
        $requestId = time();
        $requestType = "captureWallet";

        $rawSignature = "accessKey=$accessKey&amount=$amount&extraData=&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawSignature, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'Test',
            'storeId' => 'MomoTestStore',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => '',
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);

        $jsonResult = json_decode($result, true);

        return response()->json($jsonResult);
    }

    public function return(Request $request)
    {
        return response()->json($request->all());
    }

    public function notify(Request $request)
    {
        \Log::info('MOMO_NOTIFY', $request->all());
        return response()->json(['status' => 'success']);
    }
}
```

# phân tích và làm rõ code backend

## đoạn chính endpoind momo

```php
$endpoint = env('MOMO_ENDPOINT');
$partnerCode = env('MOMO_PARTNER_CODE');
$accessKey = env('MOMO_ACCESS_KEY');
$secretKey = env('MOMO_SECRET_KEY');
```

đây là code được lấy từ [`https://developers.momo.vn`](https://developers.momo.vn)

- Đăng ký tài khoản (chọn Business → Sandbox)
- Sau khi đăng nhập, vào My Projects → Create new Project
- Chọn Payment Gateway
- Ghi lại các thông tin: `partnerCode`, `accessKey`, `secretKey`
- Callback URL và Redirect URL có thể tạm để trống (hoặc điền tạm, sau sẽ dùng ngrok).

## đoạn thông tin order

```php
$orderId = time(); // Mã đơn hàng
$orderInfo = "Thanh toán demo MoMo";
$amount = "10000"; // Số tiền: 10.000 VNĐ
$redirectUrl = env('MOMO_RETURN_URL'); // URL MoMo sẽ chuyển hướng sau thanh toán
$ipnUrl = env('MOMO_NOTIFY_URL'); // URL MoMo sẽ gọi server-to-server để xác nhận
$requestId = time(); // Mã request duy nhất, ví dụ là thời gian hiện tại có thể lấy dữ liệu trong code
$requestType = "captureWallet"; // Loại thanh toán MoMo
$extraData = ""; // Dữ liệu bổ sung (nếu có)

/* 
truyền thêm extraData để MoMo trả lại giúp bạn biết đơn hàng nào đã thanh toán:

$extraData = base64_encode(json_encode(['order_id' => 101, 'user_id' => 5]));

Sau khi người dùng thanh toán xong
$order_id = json_decode(base64_decode($request->extraData))->order_id;
*/
```

## Tạo chuỗi ký và mã hóa (chống giả mạo)

```php
$rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
$signature = hash_hmac("sha256", $rawHash, $secretKey);
```


## Gửi request đến MoMo API

```php
$data = [
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    'storeId' => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
];
```

| Key           | Ý nghĩa                                                   | Ví dụ                                 |
| ------------- | --------------------------------------------------------- | ------------------------------------- |
| `partnerCode` | Mã định danh của bạn do MoMo cấp                          | `MOMOXXXX2020`                        |
| `partnerName` | Tên đối tác hiển thị trên giao diện thanh toán            | `"Test"`                              |
| `storeId`     | Tên cửa hàng / điểm bán (tuỳ chọn)                        | `"MomoTestStore"`                     |
| `requestId`   | Mã yêu cầu giao dịch (duy nhất mỗi lần gọi)               | `time()`                              |
| `amount`      | Số tiền thanh toán (đơn vị: VNĐ)                          | `"10000"`                             |
| `orderId`     | Mã đơn hàng trong hệ thống của bạn                        | `time()`                              |
| `orderInfo`   | Nội dung hiển thị cho người thanh toán                    | `"Thanh toán demo MoMo"`              |
| `redirectUrl` | URL MoMo sẽ chuyển hướng người dùng về sau khi thanh toán | `"http://localhost:8000/momo/return"` |
| `ipnUrl`      | URL MoMo gọi ngược về để xác nhận thanh toán thành công   | `"http://localhost:8000/momo/notify"` |
| `lang`        | Ngôn ngữ giao diện thanh toán                             | `"vi"`                                |
| `extraData`   | Dữ liệu mở rộng bạn muốn gửi kèm (tùy chọn)               | `""` hoặc `"order_id=101"`            |
| `requestType` | Loại thanh toán (`captureWallet`)                         | `"captureWallet"`                     |
| `signature`   | Chuỗi mã hóa xác thực (đã tạo bằng `hash_hmac`)           | `"2b1a3e4c..."`                       |
