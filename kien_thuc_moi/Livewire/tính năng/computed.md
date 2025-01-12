# [Computed Properties ](https://livewire.laravel.com/docs/computed-properties#basic-usage)

là những thuộc tính không cần lưu trữ trạng thái trực tiếp nhưng được tính toán dựa trên các thuộc tính khác.

# Ví dụ cách dùng

- Gọi biến users trong component
    ```html
    <!-- gọi proprety này $this->users -->
    @foreach ($this->users as $user) 
        <tr class="border-b hover:bg-gray-100">
            <td class="py-3 px-6 text-sm text-gray-700">{{ $user->name }}</td>
            <td class="py-3 px-6 text-sm text-gray-700">{{ $user->email }}</td>
            <td class="py-3 px-6 text-sm text-gray-700">{{ $user->created_at->format('Y-m-d') }}</td>
        </tr>
    @endforeach
    ```

- Nhưng thực tế nó không có biến đó mà nó được khai báo qua `#[Computed()]`

    ```php
    <?php

    namespace App\Livewire;
    use Livewire\Attributes\Computed;

    class ViewUser extends Component
    {
        use WithPagination;

        #[Computed()] // sử dụng như này
        public function users(){
            return User::query()->latest()->paginate(2);
        }
        
        public function render()
        {
            return view('livewire.view-user');
        }
    }

    ```

# Ghi cache

```php
/*
persist: true       => Cho phép duy trì (persist) giá trị của computed property trong session hoặc cache giữa các lần tải lại trang 

seconds: 3600       => Chỉ định thời gian (tính bằng giây) mà giá trị của Computed Property sẽ được cache.

cache:true          => Cho phép Livewire cache kết quả của phương thức users để cải thiện hiệu suất.

key: 'users'        => chỉ định một key duy nhất để Livewire hoặc cache có thể nhận diện giá trị của Computed Property này.
*/
#[Computed(persist:true, seconds:3600, cache:true, key: 'users'))]
public function users()
{
    return User::latest()
        ->where('name', 'like', "%{$this->search}%")
        ->paginate(5);
}
```

# Xóa cache 

dùng `unset`

```php
public function createPost()
    {
        if ($this->posts->count() > 10) {
            throw new \Exception('Maximum post count exceeded');
        }
 
        Auth::user()->posts()->create(...);
 
        unset($this->posts); // Xóa bộ nhớ cache của Computed Property posts
    }
 
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }
 
```

# Bỏ render

Trong trường hợp này, rõ ràng là không có `render()` phương pháp nào để truyền dữ liệu vào chế độ xem Blade.

nó sẽ lấy theo mình dùng kiểu define proprety cho global

```php
<?php
 
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;
 
class ShowPosts extends Component
{
    #[Computed]
    public function posts()
    {
        return Post::all();
    }
}
```