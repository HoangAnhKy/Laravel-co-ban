# Lý thuyết 
- Mockery là một thư viện PHP dùng để tạo “mock object” (đối tượng giả) khi viết unit test, thường dùng cùng PHPUnit trong các dự án Laravel.
- Mockery giúp giả lập một class hoặc object để test logic mà không cần phụ thuộc vào dữ liệu thật như database, API, hoặc service khác.


# Cú pháp cốt lõi của Mockery (Chỉ có 4 bước)

```php
// Bước 1: khởi tạo class giả cho class Orders
$mockModel = Mockery::mock(Orders::class);

// Bước 2: Dạy nó kịch bản
$mockModel->shouldReceive('create') // Dạy nó: "Lát phải gọi hàm create() nhé"
          ->once()                  // "Chỉ được chạy 1 lần"
          ->with([                  // "Phải nhận ĐÚNG cái mảng dữ liệu này truyền vào"
              'name' => 'Test', 
              'qty' => 10
          ])
          ->andReturn(true);        // "Sau khi diễn xong, trả về kết quả là TRUE"

// nếu không dùng with nó sẽ tự động dùng withAnyArgs() chấp nhận mọi tham số truyền vào
```

# Các hàm hay dùng


## Xác định hàm (Bước 1)

- `shouldReceive('tên_hàm')`: Báo hiệu hàm này sẽ được gọi.
- `shouldNotReceive('tên_hàm')`: Đảm bảo rằng hàm này KHÔNG BAO GIỜ được gọi (nếu gọi là test báo lỗi đỏ ngay).

## Xác định số  lần chạy (Bước 2)

- `once()`: Bắt buộc chỉ gọi 1 lần
- `twice()`: Bắt buộc gọi 2 lần
- `times(n)`: Bắt buộc gọi n lần

## Kiểm soát tham số đầu vào (Bước 3)

- `with($arg1, $arg2)`: Bắt buộc phải truyền đúng tham số này vào hàm.
- `withAnyArgs()`: Truyền tham số gì vào cũng được, không quan tâm.

## Kiểm soát kết quả trả về (Bước 4)

- `andReturn($msg)`: Trả về một giá trị cụ thể
- `andReturnNull()`: Trả về null
- `andThrow(new \Exception('Lỗi DB'))`: Giả vờ ném ra một lỗi
- `andReturnSelf()`: Trả về chính cái $mockModel để nối chuỗi, dùng để tiếp tục xử lý dữ liệu tiếp


## Bắt buộc dùng `tearDown` để dọn dẹp Mockery 

```php
use PHPUnit\Framework\TestCase;
use Mockery;

class OrderServiceTest extends TestCase
{
    // ... các hàm test_...

    protected function tearDown(): void
    {
        Mockery::close(); // Dọn dẹp Mockery
        parent::tearDown();
    }
}
```

# Ví dụ

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\OrderService;
use App\Models\Orders;
use Exception;
use Mockery;

class OrderServiceTest extends TestCase
{
    // Hàm này tự động chạy sau mỗi test case để dọn dẹp bộ nhớ (quy tắc bắt buộc khi dùng Mockery)
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * TEST CASE 1: Test kịch bản mua hàng THÀNH CÔNG
     */
    public function test_it_can_create_order_successfully()
    {
        // 1. ARRANGE
        // Tạo một Model giả (Mock)
        $mockModel = Mockery::mock(Orders::class);

        // Dạy model giả: "Khi nào bị gọi hàm create() thì cứ trả về TRUE nhé"
        $mockModel->shouldReceive('create')
            ->once() // Bắt buộc hàm này chỉ được gọi 1 lần
            ->andReturn(true);

        // Bơm model giả vào Service
        $service = new OrderService($mockModel);

        // Dữ liệu giả từ người dùng
        $payload = [
            'name' => 'Order_Test',
            'email' => 'test@gmail.com',
            'qty' => 10, // Số lượng hợp lệ (< 50)
        ];

        // 2. ACT
        $result = $service->create($payload);

        // 3. ASSERT
        // Xác nhận xem kết quả trả về có đúng là TRUE không
        $this->assertTrue($result);
    }

    /**
     * TEST CASE 2: Test kịch bản LỖI (Mua quá số lượng cho phép)
     */
    public function test_it_throws_exception_if_qty_exceeds_50()
    {
        // 1. ARRANGE
        $mockModel = Mockery::mock(Orders::class);
        $service = new OrderService($mockModel);

        $payload = [
            'name' => 'Order_Test',
            'qty' => 55, // Cố tình mua 55 cái để kích hoạt lỗi
        ];

        // 2. ASSERT (Với Exception thì phải Assert trước khi Act)
        // Báo cho PHPUnit biết: "kỳ vọng chạy lệnh dưới sẽ văng ra Exception này"
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Mua sỉ số lượng lớn vui lòng liên hệ admin, tối đa 50 SP/đơn");

        // 3. ACT
        $service->create($payload);
    }
}
```