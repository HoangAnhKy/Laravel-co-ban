---
name: vue-cdn-laravel
description: >
    Tạo UI cho dự án Laravel dùng Vue 3 CDN trong file Blade.
    Trigger khi detect-stack xác nhận Blade + Vue CDN, hoặc user yêu cầu
    tạo view/form/component có Vue nhưng không dùng Vite/SFC.
---

## Đặc trưng stack

- Options API: `data()`, `methods`, `mounted`, `watch` — không dùng `setup()`
- AJAX: **axios > $.ajax** — kiểm tra layout có axios không trước khi chọn
- Mount vào `<div id="root">`, push script vào `@push("js")`
- `@{{ }}` cho Vue expression trong Blade, `@json()` cho object/array từ Laravel

## Skeleton chuẩn

```blade
@section("content")
<div class="row" id="root">
    <div class="card-body">{{-- nội dung --}}</div>
    <div class="card-footer text-center">
        <a href="{{ route('admin.xxx.index') }}" class="btn btn-danger">Hủy</a>
        <button class="btn btn-success" @click="save">Lưu</button>
    </div>
</div>
@endsection

@push("js")
<script src="{{ asset('assets/js/vue-3-cdn.min.js') }}"></script>
<script>
window.app = Vue.createApp({
    data() { return { form: {}, errors: {} } },
    methods: {},
    mounted() {},
    watch: {}
}).mount('#root')
</script>
@endpush
```

## AJAX — axios (ưu tiên)

```js
async save() {
    try {
        const res = await axios.post("{{ route('admin.xxx.store') }}", this.form)
        showAlert(res.data.message, 'success', '#ajax-alert')
        if (res.data.data?.url) setTimeout(() => window.location.href = res.data.data.url, 3000)
    } catch (err) {
        if (err.response?.data?.errors) this.errors = err.response.data.errors
        showAlert(err.response?.data?.message ?? 'Có lỗi xảy ra', 'danger', '#ajax-alert')
    }
}
```

> **$.ajax fallback:** truyền `_token: '{{ csrf_token() }}'` trong `data`. Có nested array → `JSON.stringify` + `contentType: 'application/json'`. Cần `const self = this` để truy cập Vue instance trong callback.

## Load dữ liệu Edit

```js
mounted() {
    const record = @json($record ?? '')
    if (record !== '') this.form = { name: record.name, status: record.status }
}
```

## Dynamic table (thêm/xóa row)

```js
data() { return { form: {}, details: [], row_index: 0, errors: {} } },
methods: {
    addRow(data = {}) {
        this.details.push({ row_id: this.row_index++, product_id: data.product_id||'', qty: data.qty||'', price: data.price||'' })
    },
    deleteRow(index) {
        this.details.splice(index, 1)
        const e = { ...this.errors }
        Object.keys(e).forEach(k => { if (k.startsWith(`details.${index}`)) delete e[k] })
        this.errors = e
    }
},
watch: {
    details: {
        deep: true,
        handler(val) {
            const last = val[val.length - 1]
            if (last?.product_id) this.addRow() // tự thêm dòng trống khi dòng cuối có data
        }
    }
},
mounted() {
    this.addRow()
    const record = @json($record ?? '')
    if (record !== '') record.details.forEach(item => this.addRow(item))
}
```

## Hiển thị lỗi validation

```blade
{{-- Field đơn --}}
<input :class="{'is-invalid': errors.name}" v-model="form.name">
<div v-if="errors.name" class="invalid-feedback">@{{ errors.name[0] }}</div>

{{-- Nested array --}}
<input :class="{'is-invalid': errors['details.' + index + '.qty']}" v-model="item.qty">
<div v-if="errors['details.' + index + '.qty']" class="invalid-feedback">@{{ errors['details.' + index + '.qty'][0] }}</div>
```

## Quy tắc cứng

- Options API only — không dùng `setup()` hay Composition API
- axios CSRF: set `X-CSRF-TOKEN` header global 1 lần trong layout
- `@json()` cho object/array, `'{{ $scalar }}'` cho string/number
- jQuery chỉ dùng cho plugin (select2, datepicker...), không dùng `$.ajax` khi có axios
- Không dùng `v-html` với data từ user — XSS risk
