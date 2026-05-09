---
name: generate-code
description: >
    Tạo code backend Laravel theo kiến trúc Controller → Service → Repository → Model.
    Trigger khi user nói "tạo CRUD", "viết controller", "làm API cho", "tạo module",
    "generate code", "tạo service/repository/request", hoặc yêu cầu bất kỳ thứ gì
    liên quan đến backend Laravel. Chỉ backend — không tạo file frontend.
---

# generate-code

Sinh code backend Laravel chuẩn kiến trúc. Không tạo file frontend, không hardcode,
không raw SQL.

---

## Hỏi trước khi làm (nếu chưa rõ)

- Đây là **Web** (Blade/Inertia) hay **API** hay **cả hai**?
- Model đã có chưa hay tạo mới?
- Có cần Factory/Seeder không?

---

## Thứ tự tạo

```
1. Migration + Model
2. Factory (nếu cần)
3. Form Request
4. Repository Interface + Implementation
5. Service
6. Controller (Web / API / Inertia — theo stack)
7. API Resource (nếu là API hoặc Inertia)
8. Route
9. Bind Interface trong AppServiceProvider
```

> Dùng `artisan make:` cho tất cả — không tạo file thủ công.

```bash
php artisan make:model Product -m          # model + migration
php artisan make:request StoreProductRequest
php artisan make:request UpdateProductRequest
php artisan make:controller Web/ProductController --resource
php artisan make:controller Api/ProductController --api
php artisan make:resource ProductResource
php artisan make:job SyncProductToPosJob
```

---

## Cấu trúc thư mục

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/                        ← API controller
│   │   └── Web/                        ← Web controller (Blade/Inertia)
│   ├── Requests/
│   └── Resources/                      ← API Resource
├── Services/
├── Repositories/
│   ├── Interfaces/                     ← Interface
│   └── Eloquent/                       ← Implementation
└── Models/
```

---

## Model

```php
// app/Models/Product.php
class Product extends Model
{
    // Khai báo $fillable rõ ràng — không dùng $guarded
    protected $fillable = [
        'name',
        'price',
        'status',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relationship
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scope thường dùng
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
```

---

## Migration

```php
// database/migrations/xxxx_create_products_table.php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 12, 2)->default(0);
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
        $table->softDeletes();

        // Index cho cột thường search/filter
        $table->index(['status', 'category_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('products');
}
```

---

## Form Request

Tách riêng Store và Update để xử lý unique rule chính xác:

```php
// app/Http/Requests/StoreProductRequest.php
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // hoặc kiểm tra policy nếu cần
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:products,name'],
            'price'       => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Tên sản phẩm không được để trống.',
            'name.unique'          => 'Tên sản phẩm đã tồn tại.',
            'price.required'       => 'Giá không được để trống.',
            'category_id.exists'   => 'Danh mục không hợp lệ.',
        ];
    }
}

// app/Http/Requests/UpdateProductRequest.php
class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255',
                              Rule::unique('products', 'name')->ignore($this->route('product'))],
            'price'       => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }
}
```

---

## Repository Interface

```php
// app/Repositories/Interfaces/ProductRepositoryInterface.php
interface ProductRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): Product;
    public function create(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): bool;
}
```

---

## Repository Implementation

```php
// app/Repositories/Eloquent/ProductRepository.php
class ProductRepository implements ProductRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')                          // eager load tránh N+1
            ->when($filters['search'] ?? null, fn ($q, $v) =>
                $q->where('name', 'like', "%{$v}%"))
            ->when($filters['status'] ?? null, fn ($q, $v) =>
                $q->where('status', $v))
            ->when($filters['category_id'] ?? null, fn ($q, $v) =>
                $q->where('category_id', $v))
            ->latest()
            ->paginate(20);
    }

    public function findById(int $id): Product
    {
        return Product::with('category')->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
```

---

## Service

```php
// app/Services/ProductService.php
class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $repo
    ) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->repo->getAll($filters);
    }

    public function create(array $data): Product
    {
        $product = $this->repo->create($data);

        // Tác vụ nặng → Queue, không blocking request
        SyncProductToPosJob::dispatch($product);

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        return $this->repo->update($product, $data);
    }

    public function delete(Product $product): void
    {
        $this->repo->delete($product);
    }
}
```

---

## Controller Web (Blade)

```php
// app/Http/Controllers/Web/ProductController.php
class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $service
    ) {}

    public function index(Request $request): View
    {
        $products = $this->service->getAll(
            $request->only(['search', 'status', 'category_id'])
        );

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Thêm sản phẩm thành công.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->service->update($product, $request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Cập nhật thành công.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->service->delete($product);

        return redirect()->route('products.index')
            ->with('success', 'Đã xóa sản phẩm.');
    }
}
```

---

## Controller API

```php
// app/Http/Controllers/Api/ProductController.php
class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $service
    ) {}

    public function index(Request $request): ResourceCollection
    {
        return ProductResource::collection(
            $this->service->getAll($request->only(['search', 'status', 'category_id']))
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('category'));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        return new ProductResource(
            $this->service->update($product, $request->validated())
        );
    }

    public function destroy(Product $product): Response
    {
        $this->service->delete($product);

        return response()->noContent(); // 204
    }
}
```

---

## Controller Inertia

```php
// app/Http/Controllers/Web/ProductController.php
class ProductController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection(
                $this->service->getAll($request->only(['search', 'status']))
            ),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Thêm sản phẩm thành công.');
    }
}
```

---

## API Resource

```php
// app/Http/Resources/ProductResource.php
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'price'      => $this->price,
            'status'     => $this->status,
            'category'   => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
```

> `whenLoaded()` — chỉ include relation khi đã eager load, tránh N+1 trong Resource.

---

## Route

```php
// routes/web.php
Route::middleware(['auth'])->prefix('products')->name('products.')->group(function () {
    Route::get('/',              [Web\ProductController::class, 'index'])->name('index');
    Route::get('/create',        [Web\ProductController::class, 'create'])->name('create');
    Route::post('/',             [Web\ProductController::class, 'store'])->name('store');
    Route::get('/{product}',     [Web\ProductController::class, 'show'])->name('show');
    Route::get('/{product}/edit',[Web\ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}',     [Web\ProductController::class, 'update'])->name('update');
    Route::delete('/{product}',  [Web\ProductController::class, 'destroy'])->name('destroy');
});

// routes/api.php
Route::middleware(['auth:sanctum'])->prefix('products')->name('products.')->group(function () {
    Route::get('/',             [Api\ProductController::class, 'index'])->name('index');
    Route::post('/',            [Api\ProductController::class, 'store'])->name('store');
    Route::get('/{product}',    [Api\ProductController::class, 'show'])->name('show');
    Route::put('/{product}',    [Api\ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [Api\ProductController::class, 'destroy'])->name('destroy');
});
```

---

## Bind Interface — AppServiceProvider

```php
// app/Providers/AppServiceProvider.php
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;

public function register(): void
{
    $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

    // Thêm module mới vào đây, không để scattered trong code
}
```

---

## Quy tắc bắt buộc

- `readonly` cho dependency inject trong constructor (PHP 8.1+)
- Tách `StoreRequest` và `UpdateRequest` — không dùng chung 1 class để xử lý unique ignore
- `whenLoaded()` trong Resource thay vì load thẳng relation
- API delete trả `204 No Content` — không trả message JSON
- Tác vụ nặng trong Service → dispatch Job, không blocking
- Mọi query có filter dùng `when()` — không if/else ngoài query
- Comment bằng tiếng Việt, tên biến/function/class bằng tiếng Anh
