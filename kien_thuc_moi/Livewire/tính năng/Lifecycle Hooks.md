
## 1. `mount()`
### Khi nào được gọi:
`mount()` được gọi khi một component được tạo ra.

### Công dụng:
Thường dùng để khởi tạo dữ liệu hoặc thiết lập component.

### Ví dụ:
#### File Blade
```blade
@livewire('users', ['q' => 'q2'])
```

#### Component Livewire
```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Users extends Component
{
    public $query;

    public function mount($q)
    {
        $this->query = $q; // Gán giá trị tham số truyền vào cho thuộc tính
    }

    public function render()
    {
        return view('livewire.users', ['users' => User::where('name', 'like', "%{$this->query}%")->get()]);
    }
}
```

---

## 2. `updating()` và `updated()`
### Khi nào được gọi:
- `updating()`: Trước khi một thuộc tính của component được cập nhật.
- `updated()`: Sau khi một thuộc tính của component được cập nhật.

### Công dụng:
Sử dụng để thực hiện logic trước hoặc sau khi giá trị của thuộc tính thay đổi.

### Ví dụ:
#### Component Livewire
```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SearchUsers extends Component
{
    public $query;

    // Nếu gọi updating hoặc  updated thôi thì

    /*
        public function updated($property, $value)
        { 
            if ($property === 'username') {
                nó trùng cái nào thì mới thực thi
            }
        }

    */

    public function updatingQuery($value) // updating + proprety
    {
        // Trước khi `query` được cập nhật
        if (strlen($value) < 3) {
            $this->addError('query', 'Từ khóa phải có ít nhất 3 ký tự.');
        }
    }

    public function updatedQuery($value) // updated + proprety
    {
        // Sau khi `query` được cập nhật
        session()->flash('message', "Từ khóa tìm kiếm: $value");
    }

    public function render()
    {
        return view('livewire.search-users', [
            'users' => User::where('name', 'like', "%{$this->query}%")->get()
        ]);
    }
}
```

---

## 3. `hydrate()` và `dehydrate()`
### Khi nào được gọi:
- `hydrate()`: Khi trạng thái component được khôi phục (re-hydrated) trong một request tiếp theo.
- `dehydrate()`: Trước khi trạng thái component được gửi về frontend.

### Công dụng:
- `hydrate()`: Phục hồi trạng thái của component trước khi render.
- `dehydrate()`: Chuẩn bị dữ liệu cuối cùng trước khi gửi về trình duyệt.

### Ví dụ:
#### Component Livewire
```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function hydrate()
    {
        // Khi trạng thái component được phục hồi
        logger('Component đang được re-hydrated');
    }

    public function dehydrate()
    {
        // Trước khi gửi về frontend
        session()->flash('message', 'Component đã được cập nhật!');
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
```

---

## 4. `exception()`
### Khi nào được gọi:
Được gọi khi có ngoại lệ xảy ra trong component.

### Công dụng:
Xử lý ngoại lệ hoặc ngăn chặn việc lan truyền lỗi ra ngoài.

### Ví dụ:
#### Component Livewire
```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Exception;

class ErrorHandler extends Component
{
    public $name;

    public function save()
    {
        try {
            if (empty($this->name)) {
                throw new Exception("Tên không được để trống!");
            }
            // Giả sử lưu dữ liệu thành công
        } catch (Exception $e) {
            $this->addError('name', $e->getMessage());
        }
    }

    public function exception($e, $stopPropagation)
    {
        logger('Exception xảy ra: ' . $e->getMessage());
        $stopPropagation(); // Ngăn lỗi tiếp tục truyền ra ngoài
    }

    public function render()
    {
        return view('livewire.error-handler');
    }
}
```

---

## Tóm tắt các Lifecycle Hooks
| Hook Method         | Mô tả                                                                         |
|---------------------|-------------------------------------------------------------------------------|
| `mount()`           | Được gọi khi một component được tạo ra.                                      |
| `hydrate()`         | Được gọi khi một component được phục hồi ở đầu mỗi request tiếp theo.         |
| `boot()`            | Được gọi vào đầu mỗi request, cả lần đầu và các lần tiếp theo.                |
| `updating()`        | Được gọi trước khi một thuộc tính của component được cập nhật.                |
| `updated()`         | Được gọi sau khi một thuộc tính của component được cập nhật.                 |
| `rendering()`       | Được gọi trước khi phương thức `render()` được thực thi.                      |
| `rendered()`        | Được gọi sau khi phương thức `render()` được thực thi.                       |
| `dehydrate()`       | Được gọi vào cuối mỗi request của component trước khi gửi về frontend.       |
| `exception($e, $stopPropagation)` | Được gọi khi xảy ra ngoại lệ trong component.                                 |
