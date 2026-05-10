---
name: generate-code
description: >
    Tạo code backend Laravel theo kiến trúc Controller → Service → Repository → Model.
    Trigger khi user nói "tạo CRUD", "viết controller", "làm API", "tạo module",
    "tạo service/repository/request", hoặc bất kỳ yêu cầu backend Laravel nào.
---

## Hỏi trước khi làm

- Web (Blade/Inertia), API, hay cả hai?
- Model đã có chưa?
- Cần Factory/Seeder không?

## Thứ tự tạo

```
1. Migration + Model          php artisan make:model Product -m
2. Form Request               php artisan make:request StoreProductRequest
                              php artisan make:request UpdateProductRequest
3. Repository Interface + Impl  (tạo thủ công trong app/Repositories/)
4. Service                    (tạo thủ công trong app/Services/)
5. Controller                 php artisan make:controller Api/ProductController --api
                              php artisan make:controller Web/ProductController --resource
6. API Resource               php artisan make:resource ProductResource
7. Route
8. Bind Interface → AppServiceProvider
```

## Cấu trúc thư mục

```
app/
├── Http/Controllers/Api/        ← API
├── Http/Controllers/Web/        ← Blade/Inertia
├── Http/Requests/
├── Http/Resources/
├── Services/
├── Repositories/Interfaces/
├── Repositories/Eloquent/
└── Models/
```

## Repository Interface

```php
interface ProductRepositoryInterface {
    public function getAll(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): Product;
    public function create(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): bool;
}
```

## Repository Implementation — pattern cốt lõi

```php
public function getAll(array $filters = []): LengthAwarePaginator {
    return Product::query()
        ->with('category')                          // eager load — tránh N+1
        ->when($filters['search'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
        ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
        ->latest()->paginate(20);
}
```

## Service — pattern cốt lõi

```php
class ProductService {
    public function __construct(private readonly ProductRepositoryInterface $repo) {}

    public function create(array $data): Product {
        $product = $this->repo->create($data);
        SyncProductToPosJob::dispatch($product);   // tác vụ nặng → Queue
        return $product;
    }
}
```

## Form Request — pattern cốt lõi

```php
// Store: unique thường
'name' => ['required', 'string', 'max:255', 'unique:products,name'],

// Update: unique bỏ qua bản ghi hiện tại
'name' => ['required', 'string', 'max:255', Rule::unique('products','name')->ignore($this->route('product'))],
```

## Controller API

```php
public function store(StoreProductRequest $request): JsonResponse {
    return (new ProductResource($this->service->create($request->validated())))
        ->response()->setStatusCode(201);
}
public function destroy(Product $product): Response {
    $this->service->delete($product);
    return response()->noContent(); // 204
}
```

## Controller Web (Blade)

```php
public function store(StoreProductRequest $request): RedirectResponse {
    $this->service->create($request->validated());
    return redirect()->route('products.index')->with('success', 'Thêm thành công.');
}
```

## API Resource

```php
public function toArray(Request $request): array {
    return [
        'id'       => $this->id,
        'name'     => $this->name,
        'category' => new CategoryResource($this->whenLoaded('category')), // whenLoaded — tránh N+1
        'created_at' => $this->created_at->format('d/m/Y H:i'),
    ];
}
```

## Route

```php
// web.php
Route::middleware(['auth'])->prefix('products')->name('products.')->group(function () {
    Route::resource('/', Web\ProductController::class);
});

// api.php
Route::middleware(['auth:sanctum'])->prefix('products')->name('products.')->group(function () {
    Route::apiResource('/', Api\ProductController::class);
});
```

## AppServiceProvider

```php
public function register(): void {
    $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
}
```

## Quy tắc cứng

- `readonly` cho constructor injection (PHP 8.1+)
- Tách `StoreRequest` / `UpdateRequest` — không dùng chung để xử lý unique ignore
- `whenLoaded()` trong Resource — không load thẳng relation
- API delete → `204 No Content`, không trả JSON
- Filter trong query dùng `when()` — không if/else ngoài query
- Tác vụ nặng trong Service → dispatch Job, không blocking
