---
name: detect-stack
description: >
    Xác định frontend stack trước khi tạo bất kỳ file UI nào.
    Trigger khi user yêu cầu tạo view, page, form, component, hay bất kỳ UI nào.
---

## Ưu tiên phát hiện (dừng ở điều kiện đầu tiên khớp)

| Thứ tự | Điều kiện                                                       | Stack           |
| ------ | --------------------------------------------------------------- | --------------- |
| 1      | `resources/js/Pages/*.tsx`                                      | React + Inertia |
| 2      | `resources/js/Pages/*.vue`                                      | Vue 3 + Inertia |
| 3      | `package.json` có `"react"`                                     | React           |
| 4      | `package.json` có `"vue"`                                       | Vue 3           |
| 5      | `.blade.php` có `Vue.createApp(` / `v-model` / `v-for` / `v-if` | Blade + Vue CDN |
| 6      | Chỉ có `resources/views/*.blade.php`                            | Blade thuần     |

> ⚠️ Check Vue CDN (5) TRƯỚC Blade thuần (6) — dự án Vue CDN cũng có file `.blade.php`.

## Sau khi phát hiện

Xác nhận với user trước khi làm:

> "Dự án đang dùng **[STACK]**. Tôi sẽ tạo frontend theo stack này. Tiếp tục?"

Nếu không phát hiện được, hỏi:

> "Dự án dùng frontend nào? 1) Blade 2) Blade + Vue CDN 3) Vue 3 + Inertia 4) React + Inertia"

## Skill áp dụng theo stack

| Stack             | Skill             | Lưu ý                                       |
| ----------------- | ----------------- | ------------------------------------------- |
| Blade thuần       | `blade-ui`        | `@extends`, `{{ }}`, Bootstrap 5            |
| Blade + Vue CDN   | `vue-cdn-laravel` | `@{{ }}` tránh conflict, data qua `@json()` |
| Vue 3 / + Inertia | `vue-ui`          | SFC `.vue`, Composition API                 |
| React / + Inertia | `react-ui`        | `.tsx`, TypeScript                          |

## Quy tắc cứng

- Không giả định stack khi chưa chắc
- Không tạo file UI khi chưa xác định stack
- Dự án mix stack → hỏi rõ page đang làm dùng gì
