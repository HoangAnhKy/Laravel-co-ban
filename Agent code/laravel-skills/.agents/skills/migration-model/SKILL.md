---
name: migration-model
description: >
    Tạo migration, model, factory, seeder chuẩn Laravel 11.
    Dùng khi thêm bảng mới, chỉnh cấu trúc DB, hoặc user nói
    "tạo bảng", "tạo model", "thêm migration", "tạo factory".
---

## Migration

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->decimal('price', 10, 2);
    $table->unsignedInteger('stock')->default(0);
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->json('meta')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Index cho cột hay dùng trong where/search/sort
    $table->index(['status', 'created_at']);
    $table->fullText('name'); // nếu cần search full-text
});
```

## Model

```php
class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'slug', 'price', 'stock', 'status', 'meta',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'meta'  => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    // Scopes — phải trả về Builder để có thể chain tiếp
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeSearch(Builder $q, string $keyword): Builder
    {
        return $q->where('name', 'like', "%{$keyword}%");
    }
}
```

## Factory

```php
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name'    => fake()->words(3, true),
            'slug'    => fake()->unique()->slug(),
            'price'   => fake()->randomFloat(2, 10000, 5000000),
            'stock'   => fake()->numberBetween(0, 100),
            'status'  => fake()->randomElement(['active', 'inactive']),
        ];
    }

    // State cho test cụ thể
    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }
}
```

## Quy tắc

- `softDeletes()` cho bảng dữ liệu quan trọng
- `json()` cho dữ liệu linh hoạt, không tạo nhiều cột thừa
- `slug` nếu cần SEO-friendly URL
- Scope phải `return $q->...` — không `void`, để chain được như `Product::active()->search($kw)->paginate()`
- Factory state cho các trường hợp test đặc biệt
- Luôn có `down()` trong migration để rollback được
