Trait là một cơ chế trong PHP dùng để tái sử dụng phương thức (method) giữa các class, mà không cần kế thừa (extends).

# Cấu trúc 

```php
// app/Trait
trait TenTrait {
    public function tenPhuongThuc() {
        // logic ở đây
    }
}

class TenClass {
    use TenTrait;
    // use Loggable, Exportable, Importable; // dùng nhiều
}
```

# **Hiểu đơn giản:**

- `Class`: là "người xài".

- `Trait`: là "kho function".

- `use`: là cách "import" `trait` vào `class` như kiểu gọi `plugin`.

# Khi nào nên dùng trait?

| Trường hợp                                 | Có nên dùng Trait không? |
| ------------------------------------------ | ------------------------ |
| Tái sử dụng method ở nhiều class khác nhau | ✅                        |
| Viết các logic nhỏ, tiện ích               | ✅                        |
| Muốn inject dependency                     | (service contaner)       |