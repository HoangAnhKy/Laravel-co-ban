---
name: detect-stack
description: >
    Phát hiện stack frontend của dự án Laravel và xác nhận với user trước khi tạo
    bất kỳ file frontend nào. Bắt buộc chạy skill này TRƯỚC KHI tạo view, component,
    hay bất kỳ file UI nào. Trigger khi user yêu cầu tạo page, view, component,
    form, hay bất kỳ thứ gì liên quan đến giao diện trong dự án Laravel — kể cả
    khi user chỉ nói "tạo trang danh sách", "làm form thêm sản phẩm", v.v.
---

# detect-stack

Xác định stack frontend TRƯỚC KHI sinh bất kỳ code UI nào.

---

## Bước 1 — Tự phát hiện từ file dự án

Kiểm tra theo thứ tự ưu tiên dưới đây. Dừng lại ở điều kiện đầu tiên khớp.

| Thứ tự | Điều kiện phát hiện                                  | Stack xác định  |
| ------ | ---------------------------------------------------- | --------------- |
| 1      | `resources/js/Pages/*.tsx`                           | React + Inertia |
| 2      | `resources/js/Pages/*.vue`                           | Vue 3 + Inertia |
| 3      | `package.json` có `"react"`                          | React           |
| 4      | `package.json` có `"vue"` (không có Pages/)          | Vue 3           |
| 5      | File `.blade.php` có dấu hiệu Vue CDN (xem bên dưới) | Blade + Vue CDN |
| 6      | Chỉ có `resources/views/*.blade.php`                 | Blade thuần     |

### Dấu hiệu nhận biết Vue CDN trong Blade

Tìm bất kỳ dấu hiệu nào sau trong file `.blade.php`:

- `<script src="...vue...">` · `Vue.createApp(` · `v-model` · `v-if` · `v-for`
- `@click` · `:class` · `mounted()` · `watch:` · `methods:`

Ví dụ điển hình:

```php
<div id="root">
    <input v-model="name">
</div>
<script src="{{ asset('assets/js/vue-3-cdn.min.js') }}"></script>
<script>
Vue.createApp({
    data() { return { name: '' } }
}).mount('#root')
</script>
```

> ⚠️ Luôn check Vue CDN TRƯỚC Blade thuần — vì dự án Vue CDN cũng có file
> `.blade.php`, nếu check nhầm thứ tự sẽ nhận sai stack.

---

## Bước 2 — Nếu phát hiện được

Thông báo ngắn, chờ xác nhận trước khi làm:

> "Tôi phát hiện dự án đang dùng **[STACK]**.
> Tôi sẽ tạo frontend theo [STACK]. Bạn có muốn tiếp tục không?"

---

## Bước 3 — Nếu KHÔNG phát hiện được

Hỏi user, không được tự đoán:

> "Dự án này dùng frontend nào?
>
> 1. Blade + Bootstrap
> 2. Blade + Vue 3 CDN
> 3. Vue 3 + Inertia
> 4. React + Inertia
>
> (Gõ số hoặc tên để chọn)"

---

## Bước 4 — Sau khi xác định stack

| Stack                   | Skill áp dụng     | Convention áp dụng                                                    |
| ----------------------- | ----------------- | --------------------------------------------------------------------- |
| Blade thuần             | `blade-ui`        | `@extends/@section`, `{{ }}`, Bootstrap 5 class                       |
| Blade + Vue CDN         | `vue-cdn-laravel` | Vue qua `<script>`, `@{{ }}` tránh conflict Blade, data qua `@json()` |
| Vue 3 / Vue 3 + Inertia | `vue-ui`          | SFC `.vue`, Composition API, Inertia `<Link>`                         |
| React / React + Inertia | `react-ui`        | `.tsx`, Inertia `<Link>`, TypeScript types                            |

## Quy tắc cứng

- Không giả định stack khi chưa chắc chắn
- Không tạo file frontend khi chưa xác định stack
- Dự án mix nhiều stack → hỏi rõ page cụ thể đang làm dùng gì
