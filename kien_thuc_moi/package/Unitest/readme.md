- [Khái niệm và cấu trúc](#cấu-trúc-test-trong-laravel)
- [Khởi tạo và chạy Thử](#khởi-tạo-và-chạy-thử)

---

# Cấu trúc test trong Laravel

### **File cấu hình chính là `phpunit.xml` ở thư mục gốc Laravel.**

Laravel chia làm 2 loại test chính:

- `Feature Tests`: kiểm tra `toàn bộ chức năng` (route, middleware, controller, response, etc.)

- `Unit Tests`: kiểm tra các logic nhỏ, như `function` hoặc `service` clas

# Khởi tạo và chạy thử

### Khởi tạo

```sh
php artisan make:test ExampleTest           # Feature test
php artisan make:test ExampleTest --unit    # Unit test
```

### Chạy thử

```sh
php artisan test                                # Chạy toàn bộ
php artisan test --testsuite=Unit               # Chạy riêng với suite Unit không chạy Feature
php artisan test tests/{path}                   # Chạy file cụ thể trong tests
php artisan test --filter=test_login_successful # Chạy 1 method
```

# Một số hàm hay dùng

### Lưu ý

- Dùng trait: `DatabaseTransactions` Nếu test cần ghi `(INSERT/UPDATE)`
- Đặt tên phải có tiền tố `test_`

```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FeatureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_add_tag_to_existing_product()
    {
        $product = Product::where('name', 'Sản phẩm cũ 4 năm')->first();

        $product->tags()->create(['name' => 'Mới cập nhật']);

        $this->assertDatabaseHas('tags', ['name' => 'Mới cập nhật']);
    }
}

```

### HTTP giả lập (Laravel Feature Test)

| Hàm/Ghi chú                          | Mô tả                           |
| ------------------------------------ | ------------------------------- |
| ------------------------------------ | ------------------------------- |
| `[...]`                              | Data cần gửi                    |
| ------------------------------------ | ------------------------------- |
| `$this->get($url)`                   | Gửi request GET đến route       |
| `$this->post($url, $data)`           | Gửi request POST                |
| `$this->put($url, $data)`            | Gửi request PUT                 |
| `$this->delete($url)`                | Gửi request DELETE              |
| `$this->json('METHOD', $url, [...])` | Gửi request JSON                |
| `$this->actingAs($user)`             | Giả lập người dùng đã đăng nhập |
| `$this->withHeaders([...])`          | Gửi request kèm header          |

Ví dụ:

```php
public function test_create_user_with_token()
{
    $user = User::factory()->create();

    $headers = [
        'Authorization' => 'Bearer example-token',
        'Accept' => 'application/json',
    ];

    $data = [
        'name' => 'Nguyễn Văn A',
        'email' => 'a@example.com',
        'password' => 'secret123',
    ];

    $response = $this
        ->actingAs($user)
        ->withHeaders($headers)
        ->json('POST', '/api/users', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', ['email' => 'a@example.com']);
}
```

### Các hàm `assert` phổ biến

- Hay dùng
  | Hàm                                                                                   | Mô tả                            | Ví dụ                                        |
  | ------------------------------------------------------------------------------------- | -------------------------------- | -------------------------------------------- |
  | `assertTrue($cond)`                                                                   | Kiểm tra đúng                    | $this->assertTrue(1 == 1);                   |
  | `assertFalse($cond)`                                                                  | Kiểm tra sai                     | $this->assertFalse(false);                   |
  | `assertEquals($expected, $actual)`                                                    | So sánh giá trị (==)             | $this->assertEquals(5, 2 + 3);               |
  | `assertSame($expected, $actual)`                                                      | So sánh cả giá trị & kiểu (===)  | $this->assertSame('5', '5');                 |
  | `assertNotEquals($expected, $actual)`                                                 | Khác nhau                        | $this->assertNotEquals(1, 2);                |
  | `assertNotSame($expected, $actual)`                                                   | Khác nhau kiểu/giá trị           | $this->assertNotSame(5, '5');                |
  | `assertNull($value)`                                                                  | Giá trị null                     | $this->assertNull(null);                     |
  | `assertNotNull($value)`                                                               | Không phải null                  | $this->assertNotNull('abc');                 |
  | `assertEmpty($value)`                                                                 | Rỗng ('', [], null)              | $this->assertEmpty([]);                      |
  | `assertNotEmpty($value)`                                                              | Không rỗng                       | $this->assertNotEmpty([1]);                  |
  | `assertCount($n, $array)`                                                             | Đúng số lượng                    | $this->assertCount(3, [1,2,3]);              |
  | `assertContains($needle, $haystack)`                                                  | Mảng chứa phần tử                | $this->assertContains('a', ['a', 'b']);      |
  | `assertNotContains($needle, $haystack)`                                               | Mảng không chứa phần tử          | (chưa có ví dụ)                              |
  | `assertSessionHasErrors(['field'])`                                                   | kiểm tra validate trả về lỗi     | $response->assertSessionHasErrors(['email']) |
  | `assertRedirect('/dashboard')`  `assertSessionHas('success', 'Đăng nhập thành công')` | Test redirect và session message | $response->assertRedirect('/dashboard');     |
  | $response->assertSessionHas('success', 'Đăng nhập thành công');                       |

- Kiểm tra trong dB

  | Hàm                                     | Mô tả                | Ví dụ                                                            |
  | --------------------------------------- | -------------------- | ---------------------------------------------------------------- |
  | `assertDatabaseHas('table', [...])`     | DB có dòng này       | $this->assertDatabaseHas('users', ['email' => 'a@example.com']); |
  | `assertDatabaseMissing('table', [...])` | DB không có dòng này |                                                                  |
  | `assertSoftDeleted('table', [...])`     | Đã soft delete       |                                                                  |
  | `assertModelExists($model)`             | Model tồn tại        |                                                                  |
  | `assertModelMissing($model)`            | Model không tồn tại  |                                                                  |

- Mong đợi lỗi

  | Hàm                                        | Mô tả                | Ví dụ |
  | ------------------------------------------ | -------------------- | ----- |
  | `$this->expectException(Exception::class)` | Mong có lỗi xảy ra   |       |
  | `$this->expectExceptionMessage('text')`    | Lỗi có message       |       |
  | `$this->withoutExceptionHandling()`        | Hiện lỗi test rõ hơn |       |

```php
public function test_user_can_register()
{
    // Gửi request POST tới route đăng ký
    $response = $this->post('/register', [
        'name' => 'Nguyễn Văn A',
        'email' => 'a@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    // Kiểm tra HTTP status trả về là 302 (redirect)
    $response->assertStatus(302);

    // Kiểm tra có dữ liệu user mới trong DB
    $this->assertDatabaseHas('users', [
        'email' => 'a@example.com',
    ]);

    // Kiểm tra người dùng đã đăng nhập (Auth::check() trả về true)
    $this->assertTrue(Auth::check());

    // Kiểm tra nội dung trang sau khi login
    $home = $this->get('/home');
    $home->assertSee('Xin chào, Nguyễn Văn A');
}
```