# Tạo một Run Configuration tên web trong IDE JetBrains (PhpStorm/IntelliJ).
Nếu muốn bấm nút ▶ là tự chạy:

1. Edit Configurations...
2. Nhấn +
3. Chọn PHP Script

# Cấu hình:

```
Name: web
File: /đường/dẫn/project/artisan
Arguments: serve
Interpreter: /usr/bin/php
Working directory: /đường/dẫn/project
```