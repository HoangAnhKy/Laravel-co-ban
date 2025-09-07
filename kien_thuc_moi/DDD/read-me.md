# Khái niệm

`Domain-Driven Design`: “thiết kế phần mềm hướng miền nghiệp vụ”.

Các thành phần chính:
- `Entity` → đối tượng nghiệp vụ, có ID và hành vi.
- `Value Object` → dữ liệu thuần giá trị.
- `Aggregate / Aggregate Root` → nhóm entity quản lý nhất quán.
- `Repository` → chỗ duy nhất làm việc với DB.
- `Service (Domain Service / Application Service)` → chỗ thực hiện use case nghiệp vụ.
  - Nhiệm vụ: điều phối các bước xử lý nghiệp vụ, nhưng không chạm trực tiếp DB hay HTTP.
  - khác với service mặc định nên phải viết file class + inject vào Controller.
- `Controller` → chỉ gọi service.

# FLow

DDD flow (`Controller` → `Service` → `Entity` → `Repository` → `DB`).

# Demo 

```less
Client (Postman/Vue/Mobile)
          │
          ▼
Route (/api/orders/{id}/discount)
          │
          ▼
Controller (OrderController@applyDiscount)
          │
          ▼
Service (ApplyDiscountService@handle)
          │
          ▼
Repository (EloquentOrderRepository)
          │
          ├─> lấy Order từ DB (Eloquent Model)
          │
          └─> lưu Order đã cập nhật
          │
          ▼
Entity (Order->applyDiscount)
          │
          ▼
Database (orders.total giảm từ 1000 → 800)
```


## Tạo database và model

```sh
php artisan make:model Orders -m
```
- file `migration`
    ```php
    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->decimal('total', 10, 2);
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('orders');
        }
    };
    ```

- `Model`

    ```php
    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Orders extends Model
    {
        protected $fillable = ['total'];
    }
    ```

## Khởi tạo `Entity`

```php
// app/Domain/Order/Entities/Order.php
namespace App\Domain\Order\Entities;

class Order
{
    public function __construct(
        private int $id,
        private float $total
    ) {}

    public function getId(): int {
        return $this->id;
    }

    public function getTotal(): float {
        return $this->total;
    }

    public function applyDiscount(float $percent): void {
        $discount = $this->total * ($percent / 100);
        $this->total -= $discount;
    }
}
```

## Khởi tạo `Repository` (Interface + Implement)

- Khởi tạo Interface
    ```php
    <?php
    // app/Domain/Order/Repositories/OrderRepository.php
    namespace App\Domain\Order\Repositories;

    use App\Domain\Order\Entities\Order;

    interface OrderRepository {
        public function findById(int $id): ?Order;
        public function save(Order $order): void;
    }
    ```
- Khởi tạo các hàm thao tác với DB

    ```php
    <?php
    // app/Infrastructure/Repositories/EloquentOrderRepository.php
    namespace App\Infrastructure\Repositories;

    use App\Domain\Order\Entities\Order as OrderEntity;
    use App\Domain\Order\Repositories\OrderRepository;
    use App\Models\Order as OrderModel;

    class EloquentOrderRepository implements OrderRepository
    {
        public function findById(int $id): ?OrderEntity {
            $model = OrderModel::find($id);
            if (!$model) return null;

            return new OrderEntity($model->id, $model->total);
        }

        public function save(OrderEntity $order): void {
            $model = OrderModel::find($order->getId());
            if (!$model) return;

            $model->total = $order->getTotal();
            $model->save();
        }
    }
    ```

- Thêm khai báo `AppServiceProvider`


```php
public function register(): void {
    $this->app->bind(OrderRepository::class, EloquentOrderRepository::class);
}
```

## Khởi tạo `service`

```php
<?php
// app/Domain/Order/Services/ApplyDiscountService.php
namespace App\Domain\Order\Services;

use App\Domain\Order\Repositories\OrderRepository;

class ApplyDiscountService
{
    public function __construct(private OrderRepository $orders) {}

    public function handle(int $orderId, float $percent): void {
        $order = $this->orders->findById($orderId);
        if (!$order) {
            throw new \Exception("Order not found");
        }

        $order->applyDiscount($percent);

        $this->orders->save($order);
    }
}

```

## Khởi tạo `controller`

```php
<?php
// app/Http/Controllers/OrderController.php
namespace App\Http\Controllers;

use App\Domain\Order\Services\ApplyDiscountService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private ApplyDiscountService $service) {}

    public function applyDiscount(Request $request, int $id)
    {
        $percent = $request->input('percent', 10);

        $this->service->handle($id, $percent);

        return response()->json(['message' => 'Discount applied successfully']);
    }
}
```