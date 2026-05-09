---
name: code-review
description: >
    Review code Laravel trước khi merge PR. Kiểm tra security, performance,
    convention và logic. Trigger khi user nói "review code", "check code",
    "xem lại code", "PR này ổn chưa", "code này có vấn đề gì không",
    "merge được chưa", hoặc paste code vào và hỏi ý kiến.
---

# code-review

Review code Laravel theo 3 mức độ ưu tiên.

---

## Checklist

### 🔴 Critical (phải sửa trước khi merge)

**Security**

- [ ] SQL injection — raw query không dùng binding
- [ ] XSS — `{!! !!}` với input chưa sanitize
- [ ] Credential hardcode trong code (key, password, token)
- [ ] Route cần auth nhưng thiếu middleware
- [ ] Mass assignment không kiểm soát (`$guarded = []` hoặc thiếu `$fillable`)

**Logic & Architecture**

- [ ] Logic nghiệp vụ trong Controller thay vì Service
- [ ] Query nằm ngoài Repository
- [ ] Không validate input (thiếu Form Request)

**Performance**

- [ ] N+1 query — thiếu `with()` eager loading
- [ ] Tác vụ nặng (mail, file, API bên ngoài) không dùng Queue

---

### 🟡 Warning (nên sửa)

**Database**

- [ ] Thiếu index cho foreign key và cột thường dùng để search/filter
- [ ] Migration không có `down()`

**Code Quality**

- [ ] Thiếu type hint parameter và return type
- [ ] Exception không được xử lý (catch rỗng hoặc không có)
- [ ] Job không có `failed()` method
- [ ] Dead code hoặc commented-out code còn sót

**Convention**

- [ ] Comment bằng tiếng Anh thay vì tiếng Việt
- [ ] Tên biến/function/class bằng tiếng Việt
- [ ] Tạo file thủ công thay vì dùng `artisan make:`
- [ ] Dùng `asset()` thay vì `@vite()` trong Laravel 11
- [ ] Dùng `mix()` trong Laravel 11

---

### 🟢 Suggestion (cải thiện)

- [ ] Query lặp lại nhiều nơi → nên tạo scope
- [ ] Trả thẳng Model từ API → nên dùng API Resource
- [ ] Query tốn kém chạy nhiều lần → có thể cache
- [ ] Blade phức tạp, dùng lại nhiều nơi → nên tách component
- [ ] Thiếu test cho logic nghiệp vụ mới

---

## Format báo cáo

```
🔴 Critical
[app/Http/Controllers/OrderController.php:45] Logic tính tổng tiền đặt trong Controller → Chuyển vào OrderService
[app/Models/User.php:12] Dùng $guarded = [] thay vì $fillable → Khai báo $fillable rõ ràng

🟡 Warning
[app/Jobs/SendInvoice.php:30] Job thiếu failed() method → Thêm để xử lý khi job thất bại
[database/migrations/2024_01_01_create_orders_table.php:18] Thiếu index cho cột user_id → Thêm $table->index('user_id')

🟢 Suggestion
[app/Repositories/OrderRepository.php:22] Query where status lặp 3 nơi → Tạo scope scopeActive()

---
Tổng: X critical, Y warning, Z suggestion
```

---

## Lưu ý khi review

- Chỉ báo cáo vấn đề **thực sự có trong code được paste/đọc** — không liệt kê checklist chung chung
- Nếu code ổn, nói thẳng "Không có vấn đề critical, sẵn sàng merge"
- Đưa ra đoạn code sửa hoàn chỉnh cho critical, không bắt user tự đoán
