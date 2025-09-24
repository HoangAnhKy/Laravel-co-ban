Khởi tạo `2 source` với `2 db khác nhau`

```sh
composer create-project laravel/laravel service-a
composer create-project laravel/laravel service-b
```

# Service A (User Service)
Nó sẽ lấy user được truyền vào trong db service-a
```php
// Route/web.php
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/users/{id}', function ($id) {
    return User::findOrFail($id);
});

/*
{
    "id": 1,
    "name": "name",
    "email": "email@yopmail.com",
    "password": "123456",
    "created_at": "2025-09-02T14:16:39.000000Z",
    "updated_at": null
}
*/
```

# Service B (Order Service)
Kết quả sẽ trả về cả order và user (lấy từ service A).
```php
// Route/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/orders/{id}', function ($id) {
    // Lấy order giả lập
    $order = [
        'id' => $id,
        'product' => 'Laptop',
        'user_id' => 1
    ];

    // Gọi sang Service A để lấy thông tin user
    $user = Http::get("http://localhost:8001/api/users/{$order['user_id']}")->json();

    return [
        'order' => $order,
        'user' => $user
    ];
});



/*
{
    "order": {
        "id": "1",
        "product": "Laptop",
        "user_id": 1
    },
    "user": {
        "id": 1,
        "name": "name",
        "email": "email@yopmail.com",
        "password": "123456",
        "created_at": "2025-09-02T14:16:39.000000Z",
        "updated_at": null
    }
}
*/
```
