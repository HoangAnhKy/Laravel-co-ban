**`Components`** trong Laravel là một tính năng mạnh mẽ được sử dụng để tách và tái sử dụng mã giao diện người dùng (UI). Chúng được sử dụng chủ yếu trong Blade để tạo các thành phần HTML hoặc giao diện nhỏ gọn và dễ bảo trì.

- [khởi tạo](#lệnh-khởi-tạo)
- [Cấu trúc Component](#cấu-trúc-component)
- [Sử dụng Component trong Blade](#sử-dụng-component-trong-blade)
- [Slot trong Components](#slot-trong-components)
- [Inline Components](#inline-components)
- [Components với View Data](#components-với-view-data)
- [Truyền dữ liệu động qua Props](#truyền-dữ-liệu-động-qua-props)

Một số cú pháp như 
- `slot` là truyền từ html với text
- `message` là giá trị qua attributes của component.
- `match` thay thế cho `switch` trong code

    ```php
    $variable = match ($expression) {
        'case1' => 'value1',
        'case2' => 'value2',
        default => 'defaultValue',
    };
    ```
- `@props` Xác định các thuộc tính mà component có thể nhận và gián giá trị mặc định

    ```php
    @props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-700'])

    <div class="dropdown {{ $align }} w-{{ $width }}">
        <div class="{{ $contentClasses }}">
            {{ $slot }}
        </div>
    </div>

    ```

- `$attributes` các attributes không được định nghĩa trong @props sẽ tự động được lưu trữ trong biến $attributes.



***

# Lệnh khởi tạo 

```sh
php artisan make:component TênComponent
```

- Lệnh này sẽ tạo:

- Một file PHP trong thư mục app/View/Components.
- Một file Blade trong thư mục resources/views/components.

Lưu ý: Ngoài việc sử dụng lệnh, có thể sử dụng tùy chọn `--inline` để tạo một [Inline Components](#inline-components) (không cần file PHP logic).

# Cấu trúc Component

## File PHP (Logic của Component)
Trong file `app/View/Components/Alert.php`, có thể định nghĩa các thuộc tính và logic của component.

Ví dụ: 

```php
namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $message;

    public function __construct($type = 'info', $message = 'Default message')
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function render()
    {
        return view('components.alert');
    }
}
```

- Trong File Blade (Giao diện của Component). Ví dụ ở `resources/views/components/alert.blade.php`

```php
    <div class="alert alert-{{ $type }}">
        {{ $message }} // Giá trị qua attributes của component.
    </div>
```

# Sử dụng Component trong Blade

```php
<x-alert type="success" message="Thành công!"></x-alert>

// kết quả
// <div class="alert alert-success">Thành công!</div>
```

# Slot trong Components

Laravel hỗ trợ slot để truyền nội dung động vào component.

```php
// (resources/views/components/alert.blade.php
<div class="alert alert-{{ $type }}">
    {{ $slot }} // Nội dung HTML hoặc text bên trong component.
</div>
```

Sử dụng Component với Slot:

```php
<x-alert type="success">
    Nội dung thông báo tùy chỉnh!
</x-alert>

/*
<div class="alert alert-success">
    Nội dung thông báo tùy chỉnh!
</div>
*/
```

# Inline Components

Nếu component đơn giản, có thể tạo một inline component (không cần file PHP riêng).

Tạo inline component:

```sh
php artisan make:component TênComponent --inline
```
Inline component sẽ chỉ tạo file Blade trong `resources/views/components.`


dùng:

```html

// truyền nhiều prop
    // file button.blade.php

    @props(['type' => 'primary', 'size' => 'md'])

    <button class="btn btn-{{ $type }} btn-{{ $size }}">
        {{ $slot }}
    </button>

    // Sử dụng component

    <x-button type="success" size="lg">
        Large Success Button
    </x-button>

    // Kết quả

    <button class="btn btn-success btn-lg">
        Large Success Button
    </button>

// Truyền HTML Attributes

    // file button.blade.php

    @props(['type' => 'primary'])

    <button {{ $attributes->merge(['class' => 'btn btn-' . $type]) }}>
        {{ $slot }}
    </button>

    // Sử dụng component

    <x-button type="danger" id="delete-btn" onclick="confirm('Are you sure?')">
        Delete
    </x-button>

    // Kết quả

    <button class="btn btn-danger" id="delete-btn" onclick="confirm('Are you sure?')">
        Delete
    </button>


```


# Components với View Data

có thể truyền dữ liệu từ controller hoặc view khi sử dụng component:

Trong Controller:

```php
return view('some-view', [
    'data' => 'Some Data',
]);
```

Trong View:

```php
<x-alert :message="$data" type="info"></x-alert>
```

# Truyền dữ liệu động qua Props

Dùng dấu `:` khi giá trị là `biến` hoặc `biểu thức` PHP:


```php
<x-alert :message="$dynamicMessage" :type="'info'"></x-alert>
```
Laravel tự động thêm tiền tố `App\View\Components` vào tên component. Nếu cần thay đổi namespace hoặc thư mục, phải điều chỉnh trong `composer.json` hoặc sử dụng `View::component.`