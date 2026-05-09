---
name: vue-ui
description: Tạo UI cho dự án Laravel. Dùng khi cần tạo Page, Component, hoặc xử lý
  form/table trong Laravel. Hỗ trợ 2 stack: (1) Vue 3 + Inertia.js, (2) Blade + jQuery/axios.
  Nếu dự án có jQuery thì ưu tiên viết JS bằng jQuery; dùng axios khi không có jQuery.
  Nếu chưa rõ stack, hỏi người dùng trước khi sinh code.
---

## Xác định stack trước khi code

Hỏi hoặc kiểm tra dự án thuộc loại nào:

| Stack               | Dấu hiệu nhận biết                                                                                  |
| ------------------- | --------------------------------------------------------------------------------------------------- |
| **Vue 3 + Inertia** | Có `inertiajs/inertia-laravel` trong `composer.json`, file `*.vue` trong `resources/js/Pages/`      |
| **Blade + jQuery**  | Không có Inertia, view là `.blade.php`, có `jquery` trong `package.json` hoặc CDN trong layout      |
| **Blade + axios**   | Không có Inertia, không có jQuery, có `axios` trong `package.json` hoặc `resources/js/bootstrap.js` |

> **Ưu tiên:** Nếu dự án có cả jQuery lẫn axios → **dùng jQuery** cho mọi JS/AJAX. Chỉ dùng axios khi không có jQuery.

---

## STACK A — Vue 3 + Inertia.js

### Cấu trúc thư mục

```
resources/js/
├── Pages/
│   └── Products/
│       ├── Index.vue
│       ├── Create.vue
│       └── Edit.vue
├── Components/
│   ├── Pagination.vue
│   └── ConfirmModal.vue
├── Layouts/
│   └── AppLayout.vue
└── composables/
    └── useConfirm.js
```

### Checklist import bắt buộc

```js
// Inertia core
import { Link, router, usePage, useForm } from "@inertiajs/vue3";

// Vue core
import { ref, computed } from "vue";

// route() helper — cần package ziggy-js
// Đảm bảo app.js có: import { ZiggyVue } from 'ziggy-js'; app.use(ZiggyVue)
```

### Template — Index page

```vue
<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    products: Object, // ResourceCollection có pagination
    filters: Object,
});

// Flash message từ Laravel session()->flash()
const page = usePage();
const flash = computed(() => page.props.flash);

// Xác nhận xóa
const deleting = ref(null);
function confirmDelete(product) {
    deleting.value = product;
}
function destroy() {
    router.delete(route("products.destroy", deleting.value.id), {
        onSuccess: () => (deleting.value = null),
    });
}
</script>

<template>
    <AppLayout title="Danh sách sản phẩm">
        <!-- Flash message -->
        <div
            v-if="flash?.success"
            class="alert alert-success alert-dismissible fade show"
        >
            {{ flash.success }}
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Danh sách sản phẩm</h4>
            <Link :href="route('products.create')" class="btn btn-primary">
                Thêm mới
            </Link>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tên</th>
                            <th>Giá</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in products.data" :key="item.id">
                            <td>{{ item.id }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.price }}</td>
                            <td class="text-end">
                                <Link
                                    :href="route('products.edit', item.id)"
                                    class="btn btn-sm btn-outline-primary me-1"
                                    >Sửa</Link
                                >
                                <button
                                    @click="confirmDelete(item)"
                                    class="btn btn-sm btn-outline-danger"
                                >
                                    Xóa
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!products.data.length">
                            <td colspan="4" class="text-center text-muted py-4">
                                Chưa có dữ liệu
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <Pagination :links="products.links" class="mt-3" />

        <!-- Modal xác nhận xóa -->
        <div
            v-if="deleting"
            class="modal d-block"
            tabindex="-1"
            style="background:rgba(0,0,0,.4)"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xác nhận xóa</h5>
                    </div>
                    <div class="modal-body">
                        Bạn có chắc muốn xóa <strong>{{ deleting.name }}</strong
                        >?
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="deleting = null"
                            class="btn btn-secondary"
                        >
                            Hủy
                        </button>
                        <button @click="destroy" class="btn btn-danger">
                            Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```

### Template — Form dùng chung Create / Edit

```vue
<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    product: Object, // null nếu là Create, có data nếu là Edit
});

const form = useForm({
    name: props.product?.name ?? "",
    price: props.product?.price ?? "",
    status: props.product?.status ?? "active",
});

function submit() {
    props.product
        ? form.put(route("products.update", props.product.id))
        : form.post(route("products.store"));
}
</script>

<template>
    <AppLayout :title="product ? 'Sửa sản phẩm' : 'Thêm sản phẩm'">
        <div class="card">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label"
                            >Tên <span class="text-danger">*</span></label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            :class="[
                                'form-control',
                                { 'is-invalid': form.errors.name },
                            ]"
                        />
                        <div v-if="form.errors.name" class="invalid-feedback">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá</label>
                        <input
                            v-model="form.price"
                            type="number"
                            :class="[
                                'form-control',
                                { 'is-invalid': form.errors.price },
                            ]"
                        />
                        <div v-if="form.errors.price" class="invalid-feedback">
                            {{ form.errors.price }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select
                            v-model="form.status"
                            :class="[
                                'form-select',
                                { 'is-invalid': form.errors.status },
                            ]"
                        >
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Ngừng</option>
                        </select>
                        <div v-if="form.errors.status" class="invalid-feedback">
                            {{ form.errors.status }}
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary"
                    >
                        {{ form.processing ? "Đang lưu..." : "Lưu" }}
                    </button>
                    <Link
                        :href="route('products.index')"
                        class="btn btn-outline-secondary ms-2"
                        >Hủy</Link
                    >
                </form>
            </div>
        </div>
    </AppLayout>
</template>
```

### Component — Pagination.vue

```vue
<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({ links: Array });
</script>

<template>
    <nav v-if="links && links.length > 3">
        <ul class="pagination">
            <li
                v-for="link in links"
                :key="link.label"
                :class="[
                    'page-item',
                    { active: link.active, disabled: !link.url },
                ]"
            >
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="page-link"
                    v-html="link.label"
                />
                <span v-else class="page-link" v-html="link.label" />
            </li>
        </ul>
    </nav>
</template>
```

### Quy tắc Vue + Inertia

- Luôn import `Link` từ `@inertiajs/vue3` — không dùng `<a>` cho internal link
- Dùng `useForm()` của Inertia cho form submit — **không** dùng axios cho submit form thông thường
- Dùng `router.delete/patch/...` cho action không phải form
- `route()` cần Ziggy — kiểm tra `app.js` đã `app.use(ZiggyVue)`
- Flash message đọc từ `usePage().props.flash`
- **Ngoại lệ dùng axios:** chỉ khi cần call API phụ trợ (autocomplete, upload ảnh, load select động...). Khi đó setup CSRF một lần trong `app.js`:
    ```js
    import axios from "axios";
    axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    // hoặc nếu dùng cookie-based (Sanctum):
    axios.defaults.withCredentials = true;
    ```
    Rồi gọi bình thường trong component:
    ```js
    const res = await axios.get(route("api.products.search"), {
        params: { q: keyword },
    });
    ```
- Thứ tự ưu tiên: **Inertia router/form > axios > fetch thuần**

---

## STACK B — Blade + jQuery / axios

Dùng khi project không có Inertia (Laravel thuần, admin cũ, v.v.).

> **Ưu tiên jQuery** nếu dự án có cài. Chỉ dùng axios khi không có jQuery (`npm list jquery` hoặc kiểm tra CDN trong layout).

### Cấu trúc

```
resources/views/
└── products/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### Template — index.blade.php

```blade
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Danh sách sản phẩm</h4>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Thêm mới</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Tên</th><th>Giá</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ number_format($item->price) }}</td>
                    <td class="text-end">
                        <a href="{{ route('products.edit', $item) }}"
                           class="btn btn-sm btn-outline-primary me-1">Sửa</a>
                        <button
                            class="btn btn-sm btn-outline-danger btn-delete"
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                        >Xóa</button>
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

<div class="mt-3">{{ $products->links() }}</div>

<!-- Form xóa ẩn -->
<form id="delete-form" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
$(function () {
    $(document).on('click', '.btn-delete', function () {
        const id   = $(this).data('id')
        const name = $(this).data('name')
        if (!confirm('Xác nhận xóa "' + name + '"?')) return

        const form = $('#delete-form')
        form.attr('action', '/products/' + id)
        form.submit()
    })
})
</script>
@endpush
```

### Template — create.blade.php / edit.blade.php

```blade
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST"
              action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}">
            @csrf
            @isset($product) @method('PUT') @endisset

            <div class="mb-3">
                <label class="form-label">Tên <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $product->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror"
                />
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Giá</label>
                <input
                    type="number"
                    name="price"
                    value="{{ old('price', $product->price ?? '') }}"
                    class="form-control @error('price') is-invalid @enderror"
                />
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="active"   @selected(old('status', $product->status ?? 'active') === 'active')>Hoạt động</option>
                    <option value="inactive" @selected(old('status', $product->status ?? '') === 'inactive')>Ngừng</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ms-2">Hủy</a>
        </form>
    </div>
</div>
@endsection
```

### Quy tắc Blade + jQuery / axios

- Dùng `old()` để giữ lại giá trị sau validation fail
- Xóa record dùng form ẩn `method="DELETE"` + submit (jQuery hoặc JS thuần) — không dùng `<a>` với method DELETE
- Flash message dùng `session('success')` / `session('error')` trong Blade
- Pagination dùng `$collection->links()` — cấu hình `AppServiceProvider` nếu muốn Bootstrap pagination:
    ```php
    // AppServiceProvider::boot()
    Paginator::useBootstrapFive();
    ```

#### AJAX với jQuery (ưu tiên nếu có)

Setup CSRF một lần trong layout hoặc đầu script:

```js
$.ajaxSetup({
    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
});
```

Dùng trong Blade script:

```js
// Xóa bằng jQuery
$(document).on('click', '.btn-delete', function () {
    const id   = $(this).data('id')
    const name = $(this).data('name')
    if (!confirm('Xác nhận xóa "' + name + '"?')) return

    const $row = $(this).closest('tr')
    $.ajax({ url: '/products/' + id, method: 'DELETE' })
        .done(() => $row.remove())
        .fail(() => alert('Xóa thất bại'))
})

// POST / PUT bằng jQuery
$.post('/products', { name, price, status }, function (res) { ... })
$.ajax({ url: '/products/' + id, method: 'PUT', data: { name, price, status } })
```

#### AJAX với axios (fallback khi không có jQuery)

Setup một lần trong `bootstrap.js` hoặc đầu script:

```js
import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector(
    'meta[name="csrf-token"]',
)?.content;
```

Dùng trong Blade script:

```js
// Xóa bằng axios
document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", async function () {
        if (!confirm('Xác nhận xóa "' + this.dataset.name + '"?')) return;
        try {
            await axios.delete("/products/" + this.dataset.id);
            this.closest("tr").remove();
        } catch (e) {
            alert("Xóa thất bại");
        }
    });
});

// POST / PUT bằng axios
const res = await axios.post("/products", { name, price, status });
const res = await axios.put("/products/" + id, formData);
```

#### Thứ tự ưu tiên AJAX

**jQuery > axios > fetch thuần**
