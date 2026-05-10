---
name: migration-model
description: >
    Tạo migration, model, factory, seeder chuẩn Laravel 11.
    Trigger khi user nói "tạo bảng", "tạo model", "thêm migration", "tạo factory".
---

## Lệnh artisan

```bash
php artisan make:model Product -mf  # model + migration + factory
php artisan make:seeder ProductSeeder
```

## Migration — pattern chuẩn

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
    $table->index(['status', 'created_at']);
});
```

## Model — pattern chuẩn

```php
class Product extends Model {
    use SoftDeletes;
    protected $fillable = ['user_id', 'name', 'slug', 'price', 'stock', 'status', 'meta'];
    protected $casts = ['price' => 'decimal:2', 'meta' => 'array'];

    // Scope phải return Builder để chain được
    public function scopeActive(Builder $q): Builder { return $q->where('status', 'active'); }
    public function scopeSearch(Builder $q, string $kw): Builder { return $q->where('name', 'like', "%{$kw}%"); }
}
```

## Factory — pattern chuẩn

```php
public function definition(): array {
    return [
        'user_id' => User::factory(),
        'name'    => fake()->words(3, true),
        'slug'    => fake()->unique()->slug(),
        'price'   => fake()->randomFloat(2, 10000, 5000000),
        'stock'   => fake()->numberBetween(0, 100),
        'status'  => fake()->randomElement(['active', 'inactive']),
    ];
}
// State cho test
public function active(): static { return $this->state(['status' => 'active']); }
public function outOfStock(): static { return $this->state(['stock' => 0]); }
```

## Quy tắc cứng

- `softDeletes()` cho bảng dữ liệu quan trọng
- `$fillable` rõ ràng, không `$guarded`
- Migration phải có `down()` → `Schema::dropIfExists('products')`
- Scope phải `return $q->...` không `void`
- `json()` cho data linh hoạt thay vì nhiều cột thừa
- Index cho foreign key và cột thường dùng trong where/search/sort
