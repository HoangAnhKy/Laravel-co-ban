Sử dụng `wire:ignore` để hướng dẫn Livewire bỏ qua nội dung của một phần tử cụ thể, ngay cả khi chúng thay đổi giữa các yêu cầu.

Điều này hữu ích nhất khi làm việc với các thư viện javascript của bên thứ ba để nhập biểu mẫu tùy chỉnh, v.v.

```html
    <div wire:ignore></div>
```