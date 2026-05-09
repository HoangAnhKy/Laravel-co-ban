---
name: blade-ui
description: >
    Tạo Blade view + Bootstrap 5 cho dự án Laravel. Dùng skill này khi detect-stack
    xác nhận stack là Blade thuần, hoặc khi user yêu cầu tạo view, page, form, table,
    modal, layout — bất kỳ thứ gì liên quan đến giao diện Blade. Trigger kể cả khi
    user chỉ nói "tạo trang danh sách sản phẩm", "làm form thêm user", "sửa layout".
---

# blade-ui

Tạo Blade view chuẩn với Bootstrap 5 cho Laravel 11.

---

## Thứ tự tạo

1. Layout `layouts/app.blade.php` (nếu chưa có)
2. View chính theo CRUD: `index` → `create` → `edit` → `show`
3. Component nếu dùng lại nhiều nơi (`resources/views/components/`)
4. CSS riêng vào `resources/css/app.css` (chỉ khi Bootstrap không có sẵn)
5. JS vào `resources/js/modules/ten-module.js`, include qua `@push('scripts')`

---

## Cấu trúc thư mục

```
resources/
├── css/
│   └── app.css               ← Bootstrap import + class tùy chỉnh
├── js/
│   ├── app.js
│   └── modules/
│       └── ten-module.js     ← JS riêng cho từng module
└── views/
    ├── layouts/
    │   ├── app.blade.php
    │   └── partials/
    │       ├── navbar.blade.php
    │       └── notifications.blade.php  ← flash messages (success/error/warning/info)
    ├── components/
    └── [module]/
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── show.blade.php
```

---

## Partial notifications (flash message)

```blade
{{-- resources/views/layouts/partials/notifications.blade.php --}}
@foreach ([
    'success' => 'success',
    'error'   => 'danger',
    'warning' => 'warning',
    'info'    => 'info',
] as $key => $type)
    @if(session($key))
        <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
            {{ session($key) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach
```

Dùng trong controller:

```php
// ✅ Các key hợp lệ: success | error | warning | info
return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công.');
return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
return redirect()->back()->with('warning', 'Tồn kho sắp hết.');
return redirect()->back()->with('info', 'Đơn hàng đang được xử lý.');
```

---

## Layout chuẩn

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'App')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')

    <main class="container py-4">
        @include('layouts.partials.notifications')

        @yield('content')
    </main>

    {{-- Slot cho modal của từng page --}}
    @stack('modals')
    @stack('scripts')
</body>
</html>
```

---

## Template index (danh sách)

```blade
@extends('layouts.app')
@section('title', 'Danh sách')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Danh sách</h4>
    <a href="{{ route('module.create') }}" class="btn btn-primary">Thêm mới</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('module.edit', $item) }}"
                           class="btn btn-sm btn-outline-primary">Sửa</a>
                        <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete({{ $item->id }}, '{{ route('module.destroy', $item) }}')">
                            Xóa
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $items->links() }}</div>
@endsection

@push('modals')
    @include('components.delete-modal')
@endpush

@push('scripts')
    @vite('resources/js/modules/module.js')
@endpush
```

---

## Template form (create + edit dùng chung)

```blade
@extends('layouts.app')
@section('title', isset($item) ? 'Chỉnh sửa' : 'Thêm mới')

@section('content')
<div class="card" style="max-width: 600px">
    <div class="card-body">
        <form action="{{ isset($item) ? route('module.update', $item) : route('module.store') }}"
              method="POST">
            @csrf
            @if(isset($item)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Tên <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $item->name ?? '') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('module.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
```

---

## Component modal xóa (dùng chung)

```blade
{{-- resources/views/components/delete-modal.blade.php --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <p class="mb-3">Bạn chắc chắn muốn xóa?</p>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Hủy</button>
                </form>
            </div>
        </div>
    </div>
</div>
```

---

## JS module chuẩn

> ⚠️ **Ưu tiên:** Nếu dự án có jQuery, viết JS bằng jQuery. Kiểm tra trong `package.json` hoặc layout có `<script src="...jquery...">` trước khi viết Vanilla JS.

**Nếu có jQuery:**

```javascript
// resources/js/modules/module.js

/**
 * Mở modal xác nhận xóa và gán route đúng cho form.
 */
function confirmDelete(id, deleteUrl) {
    $("#deleteForm").attr("action", deleteUrl);
    $("#deleteModal").modal("show");
}
```

**Nếu không có jQuery:**

```javascript
// resources/js/modules/module.js

/**
 * Mở modal xác nhận xóa và gán route đúng cho form.
 *
 * @param {number} id
 * @param {string} deleteUrl - route('module.destroy', id) từ Blade
 */
function confirmDelete(id, deleteUrl) {
    document.getElementById("deleteForm").action = deleteUrl;
    new bootstrap.Modal(document.getElementById("deleteModal")).show();
}
```

> ✅ Route truyền từ Blade xuống JS qua tham số — không hardcode URL trong JS.

---

## CSS class riêng

Chỉ viết khi Bootstrap **không có sẵn**. Áp dụng theo quy tắc:

**Dùng ở 1 template** → viết thẳng trong `@push('styles')` của template đó:

```blade
@push('styles')
<style>
.product-thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
}
</style>
@endpush
```

**Dùng chung ở nhiều template** → tạo class trong `resources/css/app.css`:

```css
/* resources/css/app.css */

/* ✅ Đúng — dùng chung nhiều nơi, Bootstrap không có */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.cursor-pointer {
    cursor: pointer;
}

/* ❌ Sai — Bootstrap đã có, không viết lại */
.text-center {
    text-align: center;
}
.fw-bold {
    font-weight: bold;
}
```

---

## Quy tắc bắt buộc

- Nếu dự án có jQuery → ưu tiên viết JS bằng jQuery, không mix Vanilla JS
- Nếu không có jQuery → dùng Vanilla JS thuần
- Mọi input trong form phải có `@error` + class `is-invalid`
- Dùng `old()` để giữ giá trị khi validation fail
- `@push('scripts')` + `@vite()` thay vì `<script>` inline hoặc `asset()`
- `@push('modals')` để đẩy modal vào đúng vị trí trong layout
- Route xóa truyền từ Blade xuống JS — không hardcode URL trong file `.js`
- CSS riêng dùng ở 1 template → `@push('styles')` trong template đó, không đụng `app.css`
- CSS riêng dùng chung nhiều template → mới tạo class trong `app.css`
- Không viết lại class Bootstrap đã có
