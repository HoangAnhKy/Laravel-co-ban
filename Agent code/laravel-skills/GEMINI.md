## Nguyên tắc
- Không xóa file khi chưa hỏi user
- Không refactor code đang chạy nếu không được yêu cầu
- Comment tiếng Việt, tên biến/function/class tiếng Anh
- Hỏi frontend stack trước khi tạo bất kỳ file UI nào

## Stack
- Laravel 11 (bootstrap/app.php, không có Kernel.php)
- Bootstrap 5, Sanctum, Queue, Vite
- Frontend: blade | blade+vue-cdn | vue3+inertia | react+inertia

## Architecture
Controller → Service → Repository → Model
- Logic nghiệp vụ: Service. Query: Repository. Validate: Form Request.
- Bind Interface trong AppServiceProvider.

## Quy tắc cứng
- Tạo file bằng `artisan make:`, không tạo thủ công
- Migration phải có `up()` và `down()`
- Model dùng `$fillable`, không `$guarded`
- Cấm raw SQL — dùng Eloquent/Query Builder
- Không hardcode — dùng `.env` + `config()`
- Tác vụ nặng → Queue. Job phải có `failed()`, đặt timeout + retry.
- PSR-12, type hint đầy đủ, không dead code
- Blade có Vue bên trong → dùng `@{{ }}`

## Mindset
- N+1: luôn eager load, dùng `chunk/cursor` cho data lớn
- Debug: phân tích root cause từ stack trace, đưa code sửa hoàn chỉnh
- Bảo mật: Form Request bắt mọi edge case, chống XSS/CSRF/SQLi
- Chủ động hỏi khi yêu cầu chưa rõ, không tự suy diễn