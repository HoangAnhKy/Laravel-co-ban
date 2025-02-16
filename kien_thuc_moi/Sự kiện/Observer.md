# Khái niệm

Được dùng để “quan sát” (lắng nghe) các sự kiện của model (như created, updated, deleted, …). Điều này cho phép tách biệt logic xử lý khỏi model, giúp code gọn gàng, rõ ràng và dễ bảo trì hơn.

# Cách hoạt động

- Mỗi khi model thực hiện một hành động (tạo mới, cập nhật, xóa, …), Laravel sẽ tự động gọi các phương thức tương ứng trong Observer.

- _Ví dụ_: Khi gọi `User::create([...])`, nếu Observer có khai báo phương thức `created()`, phương thức này sẽ được gọi sau khi user tạo xong.

# Cách dùng

### Lệnh tạo

```sh
php artisan make:observer nameObserver --model=nameModel
```

```php
<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Log;

class PostObserver
{
    /**
     * Xử lý khi một Post được tạo (sau khi lưu vào DB).
     */
    public function created(Post $post)
    {
        Log::info('A post was created: ' . $post->title);
    }

    /**
     * Xử lý khi một Post đang được tạo (trước khi lưu vào DB).
     */
    public function creating(Post $post)
    {
        // Ví dụ: Tự động sinh slug từ title
        $post->slug = \Str::slug($post->title, '-');
    }

    /**
     * Xử lý khi một Post được cập nhật (sau khi lưu vào DB).
     */
    public function updated(Post $post)
    {
        Log::info('A post was updated: ' . $post->title);
    }

    //  có thể khai báo thêm các phương thức khác:
    // - creating, created
    // - updating, updated
    // - deleting, deleted
    // - restoring, restored
    // ...
}
```

### Đăng ký Observer với Model

```php
use App\Models\Post;
use App\Observers\PostObserver;

// FILE: app/Providers/AppServiceProvider.php

public function boot(): void
{
    Post::observe(PostObserver::class);
}

```
