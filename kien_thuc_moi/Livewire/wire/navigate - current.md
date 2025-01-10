# navigate

`navigate` điều hướng trang nhanh hơn nhiều, mang lại trải nghiệm giống như SPA cho người dùng.

## Cách dùng

```html
<nav>
    <a href="/" wire:navigate>Dashboard</a>
    <a href="/posts" wire:navigate>Posts</a>
    <a href="/users" wire:navigate>Users</a>
</nav>
```

## `.hover`

Bằng cách thêm .`hover` trình sửa đổi, Livewire sẽ tải trước một trang khi người dùng di chuột qua liên kết. Theo cách này, trang sẽ được tải xuống từ máy chủ khi người dùng nhấp vào liên kết.

```html
<a href="/" wire:navigate.hover>Dashboard</a>
```

# current

`Current` phát hiện và định dạng các liên kết đang hoạt động trên một trang.

## Ví dụ cách dùng

- Xác định url hiện tại đang ở đâu và tô đậm lên

```html
<nav>
    <a href="/dashboard" ... wire:current="font-bold text-zinc-800">Dashboard</a>
    <a href="/posts" ... wire:current="font-bold text-zinc-800">Posts</a>
    <a href="/users" ... wire:current="font-bold text-zinc-800">Users</a>
</nav>
```


