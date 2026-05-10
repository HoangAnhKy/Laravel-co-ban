---
name: write-tests
description: >
    Viết feature test và unit test cho Laravel. Trigger khi tạo feature mới,
    fix bug, hoặc user nói "viết test", "tạo test", "test cho", "kiểm thử".
---

## Chọn loại test

| Loại               | Khi nào dùng                                   |
| ------------------ | ---------------------------------------------- |
| Feature — Blade    | Controller trả `view()` hoặc `redirect()`      |
| Feature — API JSON | Controller trả JSON (axios/$.ajax)             |
| Unit               | Test Service/Repository riêng, mock dependency |

## RefreshDatabase vs DatabaseTransactions

- `DatabaseTransactions` → ưu tiên, rollback sau mỗi test, nhanh hơn
- `RefreshDatabase` → khi cần DB sạch hoàn toàn (test migration, seeder)

## Feature Test — Blade

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

    // Happy path
    public function test_user_tao_duoc_san_pham_hop_le(): void
    {
        $data = Product::factory()->make()->toArray();
        $this->actingAs($this->user)
            ->post(route('products.store'), $data)
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('products', ['name' => $data['name']]);
    }

    // Validation fail
    public function test_validate_bat_buoc_nhap_ten(): void
    {
        $this->actingAs($this->user)
            ->post(route('products.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    // Unauthorized
    public function test_user_khong_xoa_duoc_san_pham_cua_nguoi_khac(): void
    {
        $product = Product::factory()->create();
        $this->actingAs($this->user)
            ->delete(route('products.destroy', $product))
            ->assertForbidden();
    }
}
```

## Feature Test — API JSON

> Dùng `postJson/putJson/deleteJson` — tự set `Accept: application/json`

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

    // Happy path
    public function test_tao_don_hang_thanh_cong(): void
    {
        $data = ['order_code' => 'DH001', 'details' => [
            ['product_id' => 1, 'qty' => 2, 'price' => 100000, 'discount' => 0],
        ]];
        $this->actingAs($this->user)
            ->postJson(route('admin.order.store'), $data)
            ->assertOk()
            ->assertJsonStructure(['message', 'data']);
        $this->assertDatabaseHas('orders', ['order_code' => 'DH001']);
    }

    // Validation fail — nested array
    public function test_validate_details_nested(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('admin.order.store'), ['details' => [
                ['product_id' => '', 'qty' => 0, 'price' => ''],
            ]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['details.0.product_id', 'details.0.qty', 'details.0.price']);
    }

    // Unauthorized
    public function test_unauthenticated_bi_tu_choi(): void
    {
        $this->postJson(route('admin.order.store'), [])
            ->assertUnauthorized();
    }

    // Delete → 200 + assertDatabaseMissing
    public function test_xoa_don_hang(): void
    {
        $order = Order::factory()->create();
        $this->actingAs($this->user)
            ->deleteJson(route('admin.order.destroy', $order))
            ->assertOk();
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }
}
```

## Unit Test — mock dependency

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
        $this->repo->shouldReceive('create')->once()->andReturn($product);

        $result = $this->service->create($product->toArray());

        $this->assertEquals($product->name, $result->name);
        Queue::assertPushed(SyncProductToPosJob::class);
    }
}
```

## Quy tắc cứng

- Blade: dùng `post/get/delete` + `assertRedirect`, `assertSessionHas`, `assertViewHas`
- API: dùng `postJson/putJson/deleteJson` + `assertOk`, `assertUnprocessable`, `assertJsonPath`
- Không dùng `assertSessionHas` cho API JSON
- Nested validation: `assertJsonValidationErrors(['details.0.field'])`
- Unit test: mock mọi dependency, không đụng DB
- Tên test tiếng Việt, mô tả rõ hành vi
- Dùng Factory state thay vì tạo data thủ công
- Mỗi test chỉ assert 1 behavior
