---
name: write-tests
description:
    Viết feature test và unit test cho Laravel. Dùng khi tạo feature mới
    hoặc fix bug. Hỗ trợ cả Blade response (redirect/session) và API JSON response ($.ajax/axios).
---

## Chọn loại test phù hợp

| Loại                        | Khi nào dùng                                                  |
| --------------------------- | ------------------------------------------------------------- |
| **Feature test — Blade**    | Controller trả về `view()` hoặc `redirect()`                  |
| **Feature test — API JSON** | Controller trả về `response()->json()`, dùng với $.ajax/axios |
| **Unit test**               | Test Service/Repository riêng lẻ, mock dependency             |

---

## RefreshDatabase vs DatabaseTransactions

```php
use RefreshDatabase;      // Migrate lại toàn bộ DB — chậm hơn, dùng khi cần truncate
use DatabaseTransactions; // Rollback sau mỗi test — nhanh hơn, đủ dùng cho hầu hết case
```

> Ưu tiên `DatabaseTransactions` cho tốc độ. Dùng `RefreshDatabase` khi test cần DB sạch hoàn toàn (ví dụ: test migration, seeder).

---

## Feature Test — Blade (HTTP → DB)

```php
class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_xem_duoc_danh_sach_san_pham(): void
    {
        Product::factory()->count(3)->active()->create();

        $this->actingAs($this->user)
            ->get(route('products.index'))
            ->assertOk()
            ->assertViewHas('products');
    }

    public function test_user_tao_duoc_san_pham_hop_le(): void
    {
        $data = Product::factory()->make()->toArray();

        $this->actingAs($this->user)
            ->post(route('products.store'), $data)
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => $data['name']
        ]);
    }

    public function test_validate_bat_buoc_nhap_ten(): void
    {
        $this->actingAs($this->user)
            ->post(route('products.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_user_khong_xoa_duoc_san_pham_cua_nguoi_khac(): void
    {
        $product = Product::factory()->create(); // user khác tạo

        $this->actingAs($this->user)
            ->delete(route('products.destroy', $product))
            ->assertForbidden();
    }
}
```

---

## Feature Test — API JSON (dùng với $.ajax / axios)

Dùng `postJson/putJson/deleteJson` thay vì `post/put/delete` — tự set header `Accept: application/json`.

```php
class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_tao_don_hang_thanh_cong(): void
    {
        $data = [
            'order_code'          => 'DH001',
            'customer_id'         => Customer::factory()->create()->id,
            'shipping_address_id' => ShippingAddress::factory()->create()->id,
            'details'             => [
                ['product_id' => 1, 'qty' => 2, 'price' => 100000, 'discount' => 0],
                ['product_id' => 2, 'qty' => 1, 'price' => 200000, 'discount' => 10],
            ],
        ];

        $this->actingAs($this->user)
            ->postJson(route('admin.order.store'), $data)
            ->assertOk()
            ->assertJsonStructure(['message', 'data'])
            ->assertJsonPath('data.url', route('admin.order'));

        $this->assertDatabaseHas('orders', ['order_code' => 'DH001']);
    }

    public function test_validate_thieu_details(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('admin.order.store'), ['order_code' => 'DH001'])
            ->assertUnprocessable()              // HTTP 422
            ->assertJsonValidationErrors(['details']);
    }

    public function test_validate_details_nested(): void
    {
        $data = [
            'order_code' => 'DH001',
            'details'    => [
                ['product_id' => '', 'qty' => 0, 'price' => ''],
            ],
        ];

        $this->actingAs($this->user)
            ->postJson(route('admin.order.store'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'details.0.product_id',
                'details.0.qty',
                'details.0.price',
            ]);
    }

    public function test_cap_nhat_don_hang(): void
    {
        $order = Order::factory()->create();
        $data  = ['order_code' => 'DH-UPDATED', 'details' => []];

        $this->actingAs($this->user)
            ->putJson(route('admin.order.edit', $order), $data)
            ->assertOk()
            ->assertJsonPath('message', 'Cập nhật thành công');

        $this->assertDatabaseHas('orders', ['order_code' => 'DH-UPDATED']);
    }

    public function test_xoa_don_hang(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->user)
            ->deleteJson(route('admin.order.destroy', $order))
            ->assertOk();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_unauthenticated_bi_tu_choi(): void
    {
        $this->postJson(route('admin.order.store'), [])
            ->assertUnauthorized(); // HTTP 401
    }
}
```

---

## Unit Test (test Service, mock Repository)

```php
class ProductServiceTest extends TestCase
{
    private ProductService $service;
    private MockInterface $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo    = Mockery::mock(ProductRepositoryInterface::class);
        $this->service = new ProductService($this->repo);
    }

    public function test_tao_san_pham_va_dispatch_job(): void
    {
        Queue::fake();
        $product = Product::factory()->make();

        $this->repo->shouldReceive('create')
            ->once()
            ->andReturn($product);

        $result = $this->service->create($product->toArray());

        $this->assertEquals($product->name, $result->name);
        Queue::assertPushed(SyncProductToPosJob::class);
    }

    public function test_lay_danh_sach_co_filter(): void
    {
        $paginator = new LengthAwarePaginator([], 0, 20);

        $this->repo->shouldReceive('getAll')
            ->once()
            ->with(['status' => 'active'])
            ->andReturn($paginator);

        $this->service->getAll(['status' => 'active']);
    }
}
```
---

## Quy tắc

- **Blade response:** dùng `post/get/delete` + `assertRedirect`, `assertSessionHas`, `assertViewHas`
- **JSON response:** dùng `postJson/putJson/deleteJson` + `assertOk`, `assertUnprocessable`, `assertJsonPath`, `assertJsonValidationErrors`
- Không dùng `assertSessionHas('success')` cho API JSON — controller trả JSON không có session flash
- Validate nested array dùng `assertJsonValidationErrors(['details.0.field'])`
- Unit test: mock mọi dependency, không đụng DB
- Tên test tiếng Việt, mô tả rõ hành vi
- Dùng Factory state thay vì tạo data thủ công
- Mỗi test chỉ assert 1 behavior
- `setUp()` cho data dùng chung
- Ưu tiên `DatabaseTransactions` cho tốc độ, `RefreshDatabase` khi cần DB sạch hoàn toàn
