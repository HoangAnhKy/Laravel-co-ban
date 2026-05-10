---
name: vue-ui
description: >
    Tạo UI cho dự án Laravel dùng Vue 3 (có hoặc không có Inertia).
    Trigger khi detect-stack xác nhận Vue 3 hoặc Vue 3 + Inertia,
    hoặc user yêu cầu tạo page, component, form dùng Vue SFC.
---

## Phân biệt Vue 3 thuần vs Vue 3 + Inertia

|                | Vue 3 thuần                | Vue 3 + Inertia                |
| -------------- | -------------------------- | ------------------------------ |
| Routing        | Vue Router                 | Laravel routes + Inertia       |
| Link           | `<RouterLink>`             | `<Link>` từ `@inertiajs/vue3`  |
| Chuyển trang   | `router.push()`            | `router.visit()` hoặc `<Link>` |
| Data từ server | API call                   | Props từ controller            |
| File           | `resources/js/components/` | `resources/js/Pages/`          |

## Cấu trúc thư mục

```
resources/js/
├── Pages/                  ← Inertia pages (nếu dùng Inertia)
│   └── Products/
│       ├── Index.vue
│       ├── Create.vue
│       └── Edit.vue
├── components/             ← Shared components
│   ├── AppTable.vue
│   ├── AppModal.vue
│   └── AppPagination.vue
└── composables/            ← Tái sử dụng logic
    └── useForm.js
```

## Page component — Inertia pattern

```vue
<script setup>
import { ref, computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

// Props từ Laravel controller
const props = defineProps({
    products: Object, // LengthAwarePaginator
    filters: Object,
});

const search = ref(props.filters?.search ?? "");

// Filter có debounce — không reload cứng
function applyFilter() {
    router.get(
        route("products.index"),
        { search: search.value },
        {
            preserveState: true,
            replace: true,
        },
    );
}

function destroy(id) {
    if (!confirm("Xóa sản phẩm này?")) return;
    router.delete(route("products.destroy", id));
}
</script>

<template>
    <AppLayout title="Danh sách sản phẩm">
        <div class="d-flex justify-content-between mb-3">
            <input
                v-model="search"
                @input="applyFilter"
                type="text"
                class="form-control w-25"
                placeholder="Tìm kiếm..."
            />
            <Link :href="route('products.create')" class="btn btn-primary"
                >Thêm mới</Link
            >
        </div>

        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in products.data" :key="item.id">
                    <td>{{ item.id }}</td>
                    <td>{{ item.name }}</td>
                    <td>
                        <span
                            :class="[
                                'badge',
                                item.status === 'active'
                                    ? 'bg-success'
                                    : 'bg-secondary',
                            ]"
                        >
                            {{ item.status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <Link
                            :href="route('products.edit', item.id)"
                            class="btn btn-sm btn-outline-primary"
                            >Sửa</Link
                        >
                        <button
                            @click="destroy(item.id)"
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

        <!-- Pagination từ Inertia paginator -->
        <div class="mt-3 d-flex gap-2">
            <Link
                v-for="link in products.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="[
                    'btn btn-sm',
                    link.active ? 'btn-primary' : 'btn-outline-secondary',
                    !link.url ? 'disabled' : '',
                ]"
                v-html="link.label"
            />
        </div>
    </AppLayout>
</template>
```

## Form component — Inertia useForm

```vue
<script setup>
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({ product: Object }); // null khi create

// useForm tự xử lý loading, errors, submit
const form = useForm({
    name: props.product?.name ?? "",
    price: props.product?.price ?? "",
    status: props.product?.status ?? "active",
    category_id: props.product?.category_id ?? "",
});

function submit() {
    if (props.product) {
        form.put(route("products.update", props.product.id));
    } else {
        form.post(route("products.store"));
    }
}
</script>

<template>
    <form @submit.prevent="submit">
        <div class="mb-3">
            <label class="form-label"
                >Tên <span class="text-danger">*</span></label
            >
            <input
                v-model="form.name"
                type="text"
                :class="['form-control', form.errors.name ? 'is-invalid' : '']"
            />
            <div v-if="form.errors.name" class="invalid-feedback">
                {{ form.errors.name }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <button
                type="submit"
                class="btn btn-primary"
                :disabled="form.processing"
            >
                {{ form.processing ? "Đang lưu..." : "Lưu" }}
            </button>
            <Link
                :href="route('products.index')"
                class="btn btn-outline-secondary"
                >Hủy</Link
            >
        </div>
    </form>
</template>
```

## Controller — trả data cho Inertia

```php
// Inertia::render thay vì view()
public function index(Request $request): \Inertia\Response {
    return Inertia::render('Products/Index', [
        'products' => ProductResource::collection(
            $this->service->getAll($request->only(['search', 'status']))
        ),
        'filters' => $request->only(['search', 'status']),
    ]);
}

public function edit(Product $product): \Inertia\Response {
    return Inertia::render('Products/Edit', [
        'product' => new ProductResource($product),
    ]);
}
```

## Quy tắc cứng

- Dùng `<Link>` từ Inertia, không dùng `<a>` cho internal navigation
- Dùng `useForm()` cho mọi form submit — không dùng axios thủ công
- `router.get()` với `preserveState: true` cho filter/search — không reload cứng
- Không dùng `Options API` (`data()`, `methods:`) — dùng Composition API `<script setup>`
- Controller trả `Inertia::render()`, không trả JSON trực tiếp (trừ API riêng)
- Pagination dùng `products.links` từ Laravel paginator — không tự làm pagination
