---
name: blade-ui
description: >
    Tạo Blade view + Bootstrap 5 cho Laravel. Trigger khi detect-stack xác nhận
    Blade thuần, hoặc user yêu cầu tạo view, page, form, table, modal, layout.
---

## Thứ tự tạo

1. Layout `layouts/app.blade.php` (nếu chưa có)
2. Views: `index` → `create` → `edit` → `show`
3. Component tái sử dụng → `resources/views/components/`
4. JS riêng → `resources/js/modules/ten-module.js`, include qua `@push('scripts')`

## Cấu trúc thư mục

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── partials/notifications.blade.php
│   ├── components/delete-modal.blade.php
│   └── [module]/index | create | edit | show
├── js/modules/ten-module.js
└── css/app.css
```

## Layout — pattern chuẩn

```blade
<!DOCTYPE html><html lang="vi"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'App')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head><body>
    @include('layouts.partials.navbar')
    <main class="container py-4">
        @include('layouts.partials.notifications')
        @yield('content')
    </main>
    @stack('modals')
    @stack('scripts')
</body></html>
```

## Notifications partial

```blade
@foreach(['success'=>'success','error'=>'danger','warning'=>'warning','info'=>'info'] as $key => $type)
    @if(session($key))
        <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
            {{ session($key) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach
```

> Flash key hợp lệ: `success` | `error` | `warning` | `info`

## Index template — pattern chuẩn

```blade
@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Danh sách</h4>
    <a href="{{ route('module.create') }}" class="btn btn-primary">Thêm mới</a>
</div>
<div class="card"><div class="card-body p-0">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>#</th><th>Tên</th><th>Trạng thái</th><th></th></tr></thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td><span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">{{ $item->status }}</span></td>
                <td class="text-end">
                    <a href="{{ route('module.edit', $item) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="confirmDelete({{ $item->id }}, '{{ route('module.destroy', $item) }}')">Xóa</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
            @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
@push('modals')@include('components.delete-modal')@endpush
@push('scripts')@vite('resources/js/modules/module.js')@endpush
```

## Form template — create + edit dùng chung

```blade
@extends('layouts.app')
@section('content')
<div class="card" style="max-width:600px"><div class="card-body">
    <form action="{{ isset($item) ? route('module.update', $item) : route('module.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Tên <span class="text-danger">*</span></label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $item->name ?? '') }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('module.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </div>
    </form>
</div></div>
@endsection
```

## Delete modal component

```blade
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm"><div class="modal-content">
        <div class="modal-body text-center py-4">
            <p class="mb-3">Bạn chắc chắn muốn xóa?</p>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Xóa</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
            </form>
        </div>
    </div></div>
</div>
```

## JS module

```javascript
// Kiểm tra package.json hoặc layout có jQuery không → dùng jQuery, không thì Vanilla

// jQuery
function confirmDelete(id, url) {
    $("#deleteForm").attr("action", url);
    $("#deleteModal").modal("show");
}

// Vanilla
function confirmDelete(id, url) {
    document.getElementById("deleteForm").action = url;
    new bootstrap.Modal(document.getElementById("deleteModal")).show();
}
```

> Route truyền từ Blade xuống JS qua tham số — không hardcode URL trong `.js`

## CSS

- Dùng ở 1 template → `@push('styles')` trong template đó
- Dùng chung nhiều template → thêm vào `resources/css/app.css`
- Không viết lại class Bootstrap đã có (`text-center`, `fw-bold`...)

## Quy tắc cứng

- Mọi input phải có `@error` + class `is-invalid`
- Dùng `old()` để giữ giá trị khi validation fail
- Dùng `@vite()` không dùng `asset()` hoặc `mix()`
- `@push('modals')` và `@push('scripts')` đúng vị trí trong layout
