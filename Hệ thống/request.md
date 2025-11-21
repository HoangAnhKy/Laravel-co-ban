- [Request](#request)
- [curl](#curl)
- [HTTP](#http-client)
***

# curl
Dùng để gọi API HTTP thủ công.

## Một số  hàm cơ bản

| Hàm                   | Ý nghĩa                                 |
| --------------------- | --------------------------------------- |
| `curl_init()`         | Khởi tạo request                        |
| `curl_setopt()`       | Set các tùy chọn (URL, method, header…) |
| `curl_setopt_array()` | Set các tùy chọn dạng mảng              |
| `curl_exec()`         | Gửi request đi                          |
| `curl_getinfo()`      | Lấy thông tin từ request                |
| `curl_error()`        | Lấy lỗi dạng string                     |
| `curl_errno()`        | Lấy lỗi dạng mã số                      |
| `curl_close()`        | Giải phóng                              |

## OPTION của curl_setopt
| Option                   | Ý nghĩa                                                                | Ví dụ                                                                      |
| ------------------------ | ---------------------------------------------------------------------- | -------------------------------------------------------------------------- |
| `CURLOPT_URL`            | URL request                                                            | `curl_setopt($ch, CURLOPT_URL, "https://api.com");`                        |
| `CURLOPT_CUSTOMREQUEST`  | Set method (POST/PUT/DELETE…)                                          | `curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");`                       |
| `CURLOPT_POST`           | Bật POST                                                               | `curl_setopt($ch, CURLOPT_POST, true);`                                    |
| `CURLOPT_HTTPGET`        | Bật GET                                                                | `curl_setopt($ch, CURLOPT_HTTPGET, true);`                                 |
| `CURLOPT_NOBODY`         | Không lấy body (HEAD request)                                          | `curl_setopt($ch, CURLOPT_NOBODY, true);`                                  |
| ------------------------ | -------------------------------                                        | -------------------------------------------------------------------------- |
| `CURLOPT_POSTFIELDS`     | Gửi body (json, form-data, raw)                                        | `curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));`                |
|                          | Gửi form-data array                                                    | `curl_setopt($ch, CURLOPT_POSTFIELDS, ['name' => 'Kyha']);`                |
|                          | Upload file                                                            | `curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile("a.png")]);` |
| ------------------------ | -------------------------------                                        | -------------------------------------------------------------------------- |
| `CURLOPT_TIMEOUT`        | Timeout tổng (giây)                                                    | `curl_setopt($ch, CURLOPT_TIMEOUT, 20);`                                   |
| `CURLOPT_CONNECTTIMEOUT` | Timeout kết nối                                                        | `curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);`                             |
| `CURLOPT_TIMEOUT_MS`     | Timeout mili giây                                                      | `curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);`                              |
| ------------------------ | -------------------------------                                        | -------------------------------------------------------------------------- |
| `CURLOPT_COOKIE`         | Set cookie trực tiếp                                                   | `curl_setopt($ch, CURLOPT_COOKIE, "token=abc; id=1");`                     |
| `CURLOPT_COOKIEFILE`     | Load cookie từ file                                                    | `curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");`                      |
| `CURLOPT_COOKIEJAR`      | Ghi cookie ra file                                                     | `curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");`                       |
| ------------------------ | ---------------------------------------------------------------------- | -------------------------------------------------                          |
| `CURLOPT_FOLLOWLOCATION` | Cho phép cURL **tự động chuyển hướng** khi server trả về **301 / 302** | `curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);`                          |

## Ví dụ
```php
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL            => "https://api.example.com/users",
    CURLOPT_CUSTOMREQUEST  => "POST",
    CURLOPT_POSTFIELDS     => json_encode(["name" => "Kyha"]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        "Content-Type: application/json",
        "Authorization: Bearer xxx",
    ],
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false, // dev only
    CURLOPT_VERBOSE        => true,
]);

$response = curl_exec($ch);
$info     = curl_getinfo($ch);
$error    = curl_error($ch);

curl_close($ch);

dd($response, $info, $error);

```

# HTTP Client

dùng để gửi request HTTP (GET, POST, PUT, DELETE…) rất dễ, thay thế cURL thuần.

# Một số  hàm hay dùng 

| Hàm                                          | Mô tả                                 | Ví dụ                                                                      |
| -------------------------------------------- | ------------------------------------- | -------------------------------------------------------------------------- |
| `get($url, $query = [])`                     | Gửi GET request                       | `Http::get('https://api.com', ['page' => 1]);`                             |
| `post($url, $data = [])`                     | Gửi POST request                      | `Http::post('https://api.com', ['name'=>'Kyha']);`                         |
| `put($url, $data = [])`                      | Gửi PUT request                       | `Http::put('https://api.com/1', ['name'=>'Kyha']);`                        |
| `delete($url, $data = [])`                   | Gửi DELETE request                    | `Http::delete('https://api.com/1');`                                       |
| `patch($url, $data = [])`                    | Gửi PATCH request                     | `Http::patch('https://api.com/1', ['name'=>'Kyha']);`                      |
| `withHeaders(array $headers)`                | Thêm header                           | `Http::withHeaders(['Authorization'=>'Bearer xxx'])->get($url);`           |
| `withToken($token, $type='Bearer')`          | Thêm Authorization header             | `Http::withToken('abc123')->get($url);`                                    |
| `timeout($seconds)`                          | Timeout request                       | `Http::timeout(5)->get($url);`                                             |
| `acceptJson()`                               | Set header `Accept: application/json` | `Http::acceptJson()->get($url);`                                           |
| `retry($times, $sleep = 100)`                | Retry request khi lỗi                 | `Http::retry(3, 500)->get($url);`                                          |
| `withOptions(array $options)`                | Truyền Guzzle options trực tiếp       | `Http::withOptions(['allow_redirects'=>false])->get($url);`                |
| `attach($name, $contents, $filename = null)` | Upload file                           | `Http::attach('file', file_get_contents($path), 'image.png')->post($url);` |


# Lấy và kiểm tra dữ liệu truyền về

```php
$response = Http::get($url);

$response->status();    // 200, 404, 500
$response->body();      // Raw string
$response->json();      // Parse JSON thành array
$response->ok();        // true nếu status 200–299
$response->failed();    // true nếu status >= 400
$response->successful();// true nếu status 200–299
$response->serverError(); // true nếu status 500–599
$response->clientError(); // true nếu status 400–499
```

# ví dụ cơ bản

```php
//GET
$response = Http::get('https://api.example.com/users');
$data = $response->json(); // Lấy array từ JSON

//POST
$response = Http::post('https://api.example.com/users', [
    'name' => 'Kyha',
    'age'  => 20,
]);

$data = $response->json();

```

# getContent

Dùng để  gọi tới hàm khác xử  lý trong backend

```php
 $responseExportNote = json_decode(app()->make(DeliveryNoteDetailController::class)->exportNoteDetailPdfFile($requestExportNote)->getContent(), true);
            if (!$responseExportNote['status']) {
                throw new \Exception($responseExportNote['message']);
            }

/*
app()->make: đại diện cho chỗ  provider, cho phép gọi tới controller khác
getContent(): Phương thức này được gọi trên đối tượng Response được trả về 
*/
```

# Request

trong Laravel, có 2 cách phổ biến để lấy thông tin request

## request() helper function

```php
$request = request();
```
- Đây là helper toàn cục (global helper).
- Nó trả về instance hiện tại của `Illuminate\Http\Request` mà Laravel đang xử lý.
- Có thể gọi ở bất cứ đâu trong code (controller, model, service, v.v...).

nên dùng: Trong helper, service, hoặc chỗ không inject được

## Request được injection (thông qua Dependency Injection)

```php
use Illuminate\Http\Request;

public function store(Request $request)
{
    $data = $request->all();
}
```

Laravel container tạo ra instance `Illuminate\Http\Request` (giống loại object mà helper request() trả về), nhưng là instance cụ thể được truyền vào hàm này.

# khác nhau 

| Điểm khác              | `request()` helper                                    | Request injection                           |
| ---------------------- | ----------------------------------------------------- | ------------------------------------------- |
| **Nguồn**              | Lấy từ service container toàn cục (request hiện hành) | Được Laravel inject theo hàm controller     |
| **Context (ngữ cảnh)** | Có thể gọi ở bất kỳ đâu, kể cả ngoài controller       | Chỉ có trong các hàm có injection           |
| **Test / Mock**        | Khó mock hơn khi viết unit test                       | Dễ mock hơn vì inject qua constructor/hàm   |
| **Override**           | Không thể override instance hiện tại                  | Có thể inject custom subclass của `Request` |
| **Performance**        | Truy cập container mỗi lần gọi                        | Đã sẵn instance trong scope method          |
