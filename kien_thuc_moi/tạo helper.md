dùng để tạo hàm để dễ dàng sửa.

cách  dùng 

B1: Tạo helper trong app

B2: Lấy đường dẫn truyền vào comporser (auto load) 

```json
"autoload": {
    "psr-4": {
        "App\\": "app/"
    },
    "files": [
		// ví dụ
        "app/Helpers/custom_helper.php"
    ]
}
```

B3: sau đó chạy lệnh 
```sh
composer dump-autoload
```