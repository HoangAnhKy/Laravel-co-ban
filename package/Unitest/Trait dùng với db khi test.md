# Các Trait (Bùa chú) hay dùng trong Laravel Testing

| Trait                  | Tác dụng                                                                                                                           | Cú pháp / Ví dụ                                           |
| :--------------------- | :--------------------------------------------------------------------------------------------------------------------------------- | :-------------------------------------------------------- |
| `RefreshDatabase`      | Tự động chạy `migrate:fresh` ở bài test đầu tiên. Các test sau dùng Transaction để Rollback lại. DB luôn sạch sẽ. (Hay dùng nhất). | `use RefreshDatabase;` (Khai báo ở đầu class test)        |
| `DatabaseTransactions` | Giống `RefreshDatabase` nhưng KHÔNG chạy `migrate`. Chỉ dùng Transaction để Rollback. Tối ưu tốc độ cho DB siêu to.                | `use DatabaseTransactions;`                               |
| `DatabaseTruncation`   | (Mới Laravel 11) Dùng lệnh `TRUNCATE` xóa trắng các bảng bị tác động thay vì Rollback. Sửa lỗi khi code thực tế có dùng DB Commit. | `use DatabaseTruncation;`                                 |
| `WithoutMiddleware`    | Tắt toàn bộ Middleware (Auth đăng nhập, Token, CSRF). Bắn API thoải mái như chốn không người.                                      | `use WithoutMiddleware;`                                  |
| `WithFaker`            | Cung cấp biến `$this->faker` để sinh dữ liệu giả ngẫu nhiên (tên, email, địa chỉ, số điện thoại) cho test case.                    | `use WithFaker;`<br>`$email = $this->faker->safeEmail();` |


# ví dụ

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    // Mix các Trait lại với nhau bằng dấu phẩy
    use RefreshDatabase, WithFaker, WithoutMiddleware;

    public function test_can_create_order_with_random_data()
    {
        // Dùng Faker sinh data ngẫu nhiên
        $payload = [
            'name'  => 'Order_' . $this->faker->randomNumber(4),
            'email' => $this->faker->unique()->safeEmail(),
            'qty'   => $this->faker->numberBetween(1, 10),
        ];

        // Do có WithoutMiddleware nên bắn thẳng API không cần Auth
        $response = $this->postJson('/api/orders/create', $payload);

        $response->assertStatus(200);

        // Sau khi test chạy xong, RefreshDatabase tự động dọn rác trong DB
    }
}
```