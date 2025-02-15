# Lệnh tạo symbolic link để truy cập file từ public
Để sử dụng `Storage` để lấy file từ thư mục public, bạn cần chạy lệnh sau:

```sh
php artisan storage:link
```

## 1. **Lưu file**

Sử dụng `store()` hoặc `storeAs()` để lưu file vào thư mục được chỉ định.

```php
$request->file('photo')->store('photos'); // Lưu file vào thư mục storage/app/photos
```

Sử dụng `storeAs()` để tùy chỉnh tên file khi lưu:

```php
$request->file('photo')->storeAs('photos', 'custom_name.jpg');
```

## 2. **Lưu file vào thư mục public**

Sử dụng `public` disk để lưu file vào thư mục public.

```php
$request->file('photo')->store('photos', 'public'); // Lưu file vào storage/app/public/photos
```

Để truy cập file từ thư mục public, có thể sử dụng đường dẫn:

```php
asset('storage/photos/custom_name.jpg');
```

## 3. **Lấy file**

Để lấy file từ storage, sử dụng `Storage::get()`:

```php
use Illuminate\Support\Facades\Storage;

$content = Storage::get('file.jpg'); // Lấy nội dung của file
```

## 4. **Kiểm tra file có tồn tại**

Sử dụng `Storage::exists()` để kiểm tra file có tồn tại hay không.

```php
if (Storage::exists('file.jpg')) {
    // File tồn tại
}
```

## 5. **Xóa file**

Sử dụng `Storage::delete()` để xóa file.

```php
Storage::delete('file.jpg');
```

## 6. **Tải xuống file**

Sử dụng `Storage::download()` để tải file xuống.

```php
return Storage::download('file.jpg');
```

## 7. **Lấy URL của file**

Để lấy URL của file trong disk public:

```php
$url = Storage::url('photos/custom_name.jpg');
```

## 8. **Di chuyển file**

Sử dụng `move()` để di chuyển file từ vị trí này sang vị trí khác.

```php
Storage::move('file.jpg', 'new_location/file.jpg');
```

## 9. **Copy file**

Sử dụng `copy()` để sao chép file.

```php
Storage::copy('file.jpg', 'new_location/file.jpg');
```

## 10. **Danh sách file trong thư mục**

Sử dụng `Storage::files()` để lấy danh sách file trong một thư mục:

```php
$files = Storage::files('photos');
```

Nếu muốn lấy danh sách tất cả các file kể cả trong thư mục con, dùng `allFiles()`:

```php
$files = Storage::allFiles('photos');
```

## 11. **Tạo thư mục**

Sử dụng `Storage::makeDirectory()` để tạo một thư mục mới.

```php
Storage::makeDirectory('new_folder');
```

## 12. **Xóa thư mục**

Sử dụng `Storage::deleteDirectory()` để xóa một thư mục và toàn bộ nội dung bên trong.

```php
Storage::deleteDirectory('folder_name');
```

## 13. **Ghi nội dung vào file**

Sử dụng `Storage::put()` để ghi nội dung vào file:

```php
Storage::put('file.txt', 'Nội dung của file');
```

## 14. **Lưu file với tên ngẫu nhiên hoặc tên tùy chỉnh**

Sử dụng `Storage::putFile()` hoặc `Storage::putFileAs()`:

```php
// Lưu file với tên ngẫu nhiên
$path = Storage::putFile('uploads', $request->file('file'));

// Lưu file với tên gốc hoặc tên tùy chỉnh
$path = Storage::putFileAs('uploads', $request->file('file'), 'custom_name.jpg');
```

## 15. **Prepend và Append nội dung vào file**

- **`Storage::prepend()`**: Thêm nội dung vào đầu file.
- **`Storage::append()`**: Thêm nội dung vào cuối file.

```php
Storage::prepend('file.txt', 'Thêm vào đầu file');
Storage::append('file.txt', 'Thêm vào cuối file');
```

## 16. **Lấy kích thước và thời gian chỉnh sửa cuối cùng của file**

- **`Storage::size()`**: Lấy kích thước file.
- **`Storage::lastModified()`**: Lấy thời gian chỉnh sửa cuối cùng của file.

```php
$size = Storage::size('file.jpg');
$time = Storage::lastModified('file.jpg');
```

## 17. **Tạo URL tạm thời cho file**

Sử dụng `Storage::temporaryUrl()` để tạo URL tạm thời cho file (thường dùng với S3):

```php
$url = Storage::temporaryUrl('file.jpg', now()->addMinutes(5)); // URL có hiệu lực trong 5 phút
```

## 18. **Đọc và ghi file qua stream**

- **`Storage::readStream()`**: Đọc file qua stream.
- **`Storage::writeStream()`**: Ghi file qua stream.

```php
$stream = Storage::readStream('largefile.mp4');
Storage::writeStream('newfile.mp4', $stream);
```

## 19. **Phản hồi HTTP để hiển thị file**

Sử dụng `Storage::response()` để tạo phản hồi HTTP để hiển thị file (áp dụng cho file PDF, hình ảnh, ...):

```php
return Storage::response('file.pdf');
```

## 20. **Quản lý quyền truy cập của file (visibility)**

- **`Storage::setVisibility()`**: Đặt quyền truy cập file (public hoặc private).
- **`Storage::getVisibility()`**: Lấy quyền truy cập của file.

```php
Storage::setVisibility('file.jpg', 'public');
$visibility = Storage::getVisibility('file.jpg');
```

## 21. **Disk tùy chỉnh**

Có thể cấu hình các disk khác nhau trong `config/filesystems.php` và sử dụng chúng như sau:

```php
Storage::disk('s3')->put('file.txt', 'Nội dung');
```
