# Cài đặt Livewire

```sh
composer require livewire/livewire
```

# Tạo thành phần Livewire

```sh
php artisan make:livewire [name]
// php artisan make:livewire counter
```

- Lệnh này sẽ tạo ra hai tệp mới trong dự án:

    - app/Livewire/Counter.php

    - resources/views/livewire/counter.blade.php

# viết logic thực thi

Mở `app/Livewire/Counter.php` và thay thế nội dung của nó

```php
<?php
 
namespace App\Livewire;
 
use Livewire\Component;
 
class Counter extends Component
{
    public $count = 1;
 
    public function increment()
    {
        $this->count++;
    }
 
    public function decrement()
    {
        $this->count--;
    }
    
    /*
        phương thức trả về chế độ xem Blade, nó sẽ chứa các logic của Class này
    */
    public function render()
    {
        return view('livewire.counter');
    }
}
```
# Sử dụng ở View
Mở `resources/views/livewire/counter.blade.php` và thay thế nội dung của nó
```php
<div>
    <h1>{{ $count }}</h1>
 
    <button wire:click="increment">+</button>
 
    <button wire:click="decrement">-</button>
</div>
```

# Đăng ký tuyến đường với Web

- Giống với laravel thường 

    ```php
    use App\Livewire\Counter;
    
    Route::get('/counter', Counter::class);
    ```

# Tạo một bố cục mẫu

- lệnh tạo bố cục, nó sẽ tạo bố cục ở `resources/views/components/layouts/app.blade.php`

    ```sh
    php artisan livewire:layout
    ```

- nội dung trong `resources/views/components/layouts/app.blade.php`

    ```HTML
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
            <title>{{ $title ?? 'Page Title' }}</title>
        </head>
        <body>
            {{ $slot }}
            <!-- Giống như yield của laravel nó sẽ thay thế tệp kế thừa vào-->
        </body>
    </html>
    ```

# Xác định layout

```php
use Livewire\Attributes\Layout;

#[Layout("components.layouts.app")] // layout mặc định
class ViewUser extends Component{

}
```