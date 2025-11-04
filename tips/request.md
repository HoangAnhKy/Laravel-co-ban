# khái niệm

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
