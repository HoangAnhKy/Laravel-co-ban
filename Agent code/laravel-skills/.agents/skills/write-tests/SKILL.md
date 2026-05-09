---
name: write-tests
description: Viết feature test và unit test cho Laravel.
Dùng khi tạo feature mới hoặc fix bug.
---

## Feature Test (test từ HTTP đến DB)

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

## Quy tắc

- Feature test: test HTTP → DB, dùng actingAs()
- Unit test: mock mọi dependency, không đụng DB
- Tên test tiếng Việt, mô tả rõ hành vi
- Dùng Factory state thay vì tạo data thủ công
- Mỗi test chỉ assert 1 behavior
- setUp() cho data dùng chung
