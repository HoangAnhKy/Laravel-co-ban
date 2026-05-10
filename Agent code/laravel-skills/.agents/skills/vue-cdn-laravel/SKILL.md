---
name: vue-cdn-laravel
description: >
    Tạo UI cho dự án Laravel dùng Vue 3 CDN trong file Blade.
    Trigger khi detect-stack xác nhận Blade + Vue CDN, hoặc user yêu cầu
    tạo view/form/component có Vue nhưng không dùng Vite/SFC.
---

## Đặc trưng stack này

- Vue mount trực tiếp trong `.blade.php` qua CDN, không có SFC `.vue`
- Dùng `@{{ }}` thay `{{ }}` để tránh conflict với Blade
- Data từ Laravel xuống Vue qua `@json()` hoặc API call
- Không có `<script setup>`, không có `import`

## Cấu trúc file

```blade
@extends('layouts.app')
@section('content')

<div id="app">
    {{-- Template Vue viết ở đây --}}
    <div v-if="loading">Đang tải...</div>
    <div v-else>
        <p>@{{ message }}</p>
        <input v-model="search" type="text" class="form-control">
        <ul>
            <li v-for="item in filteredItems" :key="item.id">@{{ item.name }}</li>
        </ul>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        // Nhận data từ Laravel
        const items = ref(@json($items ?? []));
        const search = ref('');
        const loading = ref(false);

        const filteredItems = computed(() =>
            items.value.filter(i => i.name.toLowerCase().includes(search.value.toLowerCase()))
        );

        // Hoặc fetch từ API
        async function fetchItems() {
            loading.value = true;
            const res = await fetch('{{ route("api.items.index") }}', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            items.value = await res.json();
            loading.value = false;
        }

        onMounted(fetchItems);

        return { items, search, loading, filteredItems };
    }
}).mount('#app');
</script>
@endpush
```

## Truyền data Laravel → Vue

```blade
{{-- Cách 1: qua @json() — dùng khi data có sẵn từ controller --}}
const items = ref(@json($items));
const config = ref(@json(['route' => route('products.store'), 'perPage' => 20]));

{{-- Cách 2: fetch API — dùng khi data cần load động --}}
const res = await fetch('{{ route("api.products.index") }}');
```

## Gửi form / POST request

```javascript
async function submit() {
    const res = await fetch('{{ route("products.store") }}', {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify(form.value),
    });
    const data = await res.json();
    if (!res.ok) {
        errors.value = data.errors; // Laravel validation errors
    }
}
```

## Hiển thị lỗi validation

```blade
<input v-model="form.name" type="text"
       :class="['form-control', errors.name ? 'is-invalid' : '']">
<div v-if="errors.name" class="invalid-feedback">@{{ errors.name[0] }}</div>
```

## Quy tắc cứng

- Luôn dùng `@{{ }}` trong Blade, không dùng `{{ }}` cho Vue expression
- CSRF token bắt buộc trong mọi POST/PUT/DELETE request
- Không mix `@json()` và fetch API cho cùng 1 dataset
- Data từ `@json()` là static (render lúc load) — dùng fetch nếu cần realtime
- Không dùng `Vue.createApp({data() {...}})` — dùng Composition API `setup()`
- Script CDN đặt trong `@push('scripts')`, không inline trong `<head>`
