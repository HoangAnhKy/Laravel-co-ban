---
name: code-review
description: >
    Review code Laravel trước khi merge PR. Trigger khi user nói "review code",
    "check code", "xem lại code", "PR này ổn chưa", "merge được chưa",
    hoặc paste code và hỏi ý kiến.
---

## Checklist

### 🔴 Critical — phải sửa trước khi merge

- Raw query không dùng binding → SQL injection
- `{!! !!}` với input chưa sanitize → XSS
- Credential hardcode trong code
- Route cần auth nhưng thiếu middleware
- `$guarded = []` hoặc thiếu `$fillable` → mass assignment
- Logic nghiệp vụ trong Controller thay vì Service
- Query nằm ngoài Repository
- Không validate input (thiếu Form Request)
- N+1 query — thiếu `with()` eager loading
- Tác vụ nặng (mail, file, API ngoài) không dùng Queue

### 🟡 Warning — nên sửa

- Thiếu index cho foreign key và cột thường search/filter
- Migration không có `down()`
- Thiếu type hint parameter và return type
- Exception không được xử lý (catch rỗng)
- Job không có `failed()` method
- Dead code hoặc commented-out code
- Comment tiếng Anh thay vì tiếng Việt
- Tên biến/function/class tiếng Việt
- Dùng `asset()` thay vì `@vite()` trong Laravel 11
- Dùng `mix()` trong Laravel 11

### 🟢 Suggestion — cải thiện

- Query lặp nhiều nơi → tạo scope
- Trả thẳng Model từ API → dùng API Resource
- Query tốn kém chạy nhiều lần → cache
- Blade phức tạp, dùng lại nhiều → tách component
- Thiếu test cho logic nghiệp vụ mới

## Format báo cáo

```
🔴 Critical
[app/Http/Controllers/OrderController.php:45] Logic tính tổng tiền trong Controller → Chuyển vào OrderService
[app/Models/User.php:12] $guarded = [] → Khai báo $fillable rõ ràng

🟡 Warning
[app/Jobs/SendInvoice.php:30] Thiếu failed() method
[database/migrations/xxxx_create_orders_table.php:18] Thiếu index cho user_id

🟢 Suggestion
[app/Repositories/OrderRepository.php:22] Query where status lặp 3 nơi → scopeActive()

---
Tổng: X critical, Y warning, Z suggestion
```

## Quy tắc cứng

- Chỉ báo vấn đề **thực sự có trong code** — không liệt kê checklist chung chung
- Nếu code ổn → nói thẳng "Không có vấn đề critical, sẵn sàng merge"
- Critical → đưa code sửa hoàn chỉnh, không để user tự đoán
