## Nguyên tắc
- Không xóa file khi chưa hỏi user
- Không refactor code đang chạy nếu không được yêu cầu
- Comment bằng tiếng Việt, tên biến/function/class bằng tiếng Anh
- Luôn hỏi user là fontend dùng loại nào vue, reactjs hay là blade...

## Stack
- Laravel 11 (dùng bootstrap/app.php, không có Kernel.php)
- Bootstrap 5
- Sanctum, Queue, Vite
- Font-end có thể là vue, vue-cdn, reactjs, blade (kiểm tra kỹ lựa chọn của user)

## Architecture: Controller → Service → Repository → Model
- Logic nghiệp vụ trong Service, không trong Controller
- Validate bằng Form Request
- Query chỉ trong Repository
- Bind Interface trong AppServiceProvider

## Laravel
- Tạo file bằng artisan, không tạo thủ công
- Migration phải có up() và down()
- $fillable trong Model, không $guarded
- Raw SQL bị cấm, dùng Eloquent/Query Builder
- Không hardcode, dùng .env và config()
- Chú ý khả năng tương thích của Type Hint (PHP 7.4/8.0 nếu là Laravel 8).

## Queue
- Tác vụ nặng bắt buộc vào Queue
- Job phải có failed() method
- Đặt timeout và retry phù hợp

## Code
- PSR-12, type hint đầy đủ
- Không dead code, không commented-out code
- Nếu người dùng có sử  dụng `.blade.php` mà code vue bên trong đó thì chú ý dùng `@{{ }}`

## Kỹ năng (Skills) & Tư duy (Mindset)
- **Phân tích & Thiết kế:** Có tư duy thiết kế luồng xử lý cho các hệ thống quản lý phức tạp (ERP, SaaS, quản lý kho, đơn hàng). Chia nhỏ logic nghiệp vụ một cách triệt để vào Service.
- **Tối ưu hóa hiệu suất:** Chủ động phòng tránh N+1 query trong Eloquent. Biết cách dùng chunk/cursor khi xử lý dữ liệu lớn để tránh tràn RAM.
- **Debug & Sửa lỗi:** Khi user đưa ra dòng lỗi (stack trace), tập trung phân tích nguyên nhân gốc rễ (root cause) ở file/dòng nào. Đưa ra đoạn code sửa hoàn chỉnh, không bắt user chắp vá.
- **Bảo mật:** Luôn cảnh giác với dữ liệu đầu vào. Đảm bảo Form Request bắt mọi case ngoại lệ. Code an toàn trước XSS, CSRF, và SQL Injection.
- **Tư duy làm việc:** Tìm hiểu sâu vấn đề và chủ động hỏi lại khi yêu cầu chưa rõ ràng hoặc thiếu ngữ cảnh, tuyệt đối không tự ý suy diễn hoặc bịa ra logic.