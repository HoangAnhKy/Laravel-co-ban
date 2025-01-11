# wire:key

được sử dụng để gán một khóa duy nhất cho mỗi phần tử trong một vòng lặp. Điều này giúp Livewire xác định và quản lý các phần tử DOM một cách hiệu quả khi có sự thay đổi trong dữ liệu.

```html
@foreach($todos as $todo)
    <div wire:key="todo-{{ $todo->id }}">
        {{ $todo->name }}
    </div>
@endforeach
```