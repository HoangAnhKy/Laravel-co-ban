## 1. **`file_exists()`**
Kiểm tra xem một file hoặc thư mục có tồn tại hay không.
```php
if (file_exists('path/to/file.txt')) {
    echo "File tồn tại.";
}
```

## 2. **`is_file()`**
Kiểm tra xem đường dẫn có phải là file hay không.
```php
if (is_file('path/to/file.txt')) {
    echo "Đây là file.";
}
```

## 3. **`is_dir()`**
Kiểm tra xem đường dẫn có phải là thư mục hay không.
```php
if (is_dir('path/to/directory')) {
    echo "Đây là thư mục.";
}
```

## 4. **`move_uploaded_file()`**
Di chuyển một file đã được tải lên từ thư mục tạm thời đến một vị trí khác.
```php
if (move_uploaded_file($_FILES['file']['tmp_name'], 'path/to/destination')) {
    echo "File đã được di chuyển.";
}
```

## 5. **`unlink()`**
Xóa một file.
```php
if (unlink('path/to/file.txt')) {
    echo "File đã bị xóa.";
}
```

## 6. **`copy()`**
Sao chép một file từ vị trí này sang vị trí khác.
```php
if (copy('path/to/source.txt', 'path/to/destination.txt')) {
    echo "File đã được sao chép.";
}
```

## 7. **`rename()`**
Đổi tên hoặc di chuyển một file/thư mục.
```php
if (rename('path/to/old_name.txt', 'path/to/new_name.txt')) {
    echo "File đã được đổi tên.";
}
```

## 8. **`fopen()`**
Mở một file để đọc, ghi hoặc thêm dữ liệu.
```php
$file = fopen('path/to/file.txt', 'r'); // Mở file ở chế độ đọc
```

## 9. **`fwrite()`**
Ghi nội dung vào một file đã được mở bằng `fopen()`.
```php
$file = fopen('path/to/file.txt', 'w');
fwrite($file, "Nội dung mới"); // Ghi nội dung mới vào file
fclose($file); // Đóng file sau khi ghi
```

## 10. **`fread()`**
Đọc nội dung từ một file đã được mở bằng `fopen()`.
```php
$file = fopen('path/to/file.txt', 'r');
$content = fread($file, filesize('path/to/file.txt')); // Đọc toàn bộ nội dung file
fclose($file);
```

## 11. **`fclose()`**
Đóng file đã mở với `fopen()`.
```php
fclose($file);
```

## 12. **`file_get_contents()`**
Đọc toàn bộ nội dung của file vào một chuỗi mà không cần `fopen()`.
```php
$content = file_get_contents('path/to/file.txt');
echo $content;
```

## 13. **`file_put_contents()`**
Ghi dữ liệu vào file. Nếu file không tồn tại, nó sẽ được tạo.
```php
file_put_contents('path/to/file.txt', "Nội dung mới"); // Ghi nội dung mới vào file
```

## 14. **`filesize()`**
Trả về kích thước của file (tính theo byte).
```php
echo filesize('path/to/file.txt');
```

## 15. **`filemtime()`**
Trả về thời gian sửa đổi cuối cùng của file.
```php
echo date("F d Y H:i:s.", filemtime('path/to/file.txt'));
```

## 16. **`is_writable()`**
Kiểm tra xem file có thể ghi được không.
```php
if (is_writable('path/to/file.txt')) {
    echo "File có thể ghi.";
}
```

## 17. **`is_readable()`**
Kiểm tra xem file có thể đọc được không.
```php
if (is_readable('path/to/file.txt')) {
    echo "File có thể đọc.";
}
```

## 18. **`scandir()`**
Liệt kê tất cả các file và thư mục trong một thư mục.
```php
$files = scandir('path/to/directory');
print_r($files);
```

## 19. **`chmod()`**
Thay đổi quyền truy cập của file.
```php
chmod('path/to/file.txt', 0755);
```

## 20. **`rmdir()`**
Xóa một thư mục trống.
```php
rmdir('path/to/directory');
```
## 21. down file

```php
<?php
// Đường dẫn đến file mà bạn muốn người dùng tải xuống
$file = 'path/to/your/file.txt';

// Kiểm tra xem file có tồn tại không
if (file_exists($file)) {
    // Đặt header cho việc tải file xuống
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));

    // Đọc file và gửi đến người dùng
    readfile($file);
    exit;
} else {
    echo "File không tồn tại.";
}
```