---
name: vue-cdn-laravel
description: Tạo UI cho dự án Laravel dùng Vue 3 CDN + Blade + jQuery. Dùng khi
  view là file .blade.php, Vue được load qua CDN (không có Vite/Inertia), và jQuery
  có sẵn. Pattern này mount Vue vào một #root div trong Blade, dùng $.ajax để gọi API,
  và dùng @{{ }} để escape Blade trong template Vue.
---

## Pattern tổng quan

```
Blade (.blade.php)
├── HTML template (có v-model, v-for, @click, :class...)
├── @push("js")
│   ├── <script src="vue-3-cdn.min.js">
│   └── <script> Vue.createApp({...}).mount('#root')
└── @push("css") nếu cần style riêng
```

**Quy tắc cốt lõi:**

- Vue mount vào `<div id="root">` trong `@section("content")`
- Dùng `@{{ }}` thay cho `{{ }}` trong template — để Blade không parse directive của Vue
- Dữ liệu từ Laravel truyền vào qua `@json($var)` trong script
- CSRF token lấy từ `'{{ csrf_token() }}'` (Blade render trước khi Vue chạy)
- jQuery dùng cho AJAX (`$.ajax`) và các plugin bên thứ ba (select2, datepicker...)
- **Không** dùng Vite, `import`, hay `<script setup>`

---

## Cấu trúc file Blade chuẩn

```blade
@extends("Layout.defaultAdmin")

@push("css")
<style>
    /* style riêng của trang nếu cần */
</style>
@endpush

@section("content")
{{-- Breadcrumb / page title --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @foreach ($breadcrumb as $item)
                        @if(!empty($item["url"]))
                            <li class="breadcrumb-item"><a href="{{ $item["url"] }}">{{ $item["name"] }}</a></li>
                        @else
                            <li class="breadcrumb-item active">{{ $item["name"] }}</li>
                        @endif
                    @endforeach
                </ol>
            </div>
            <h4 class="page-title">{{ $page_title ?? '' }}</h4>
        </div>
    </div>
</div>

{{-- Vue root --}}
<div class="row" id="root">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                {{-- Nội dung form / table ở đây --}}

            </div>
            <div class="card-footer text-center">
                <a href="{{ route('admin.xxx.index') }}" class="btn btn-danger mx-2">Hủy</a>
                <button class="btn btn-success" @click="save">Lưu</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push("js")
<script src="{{ asset('assets/js/vue-3-cdn.min.js') }}"></script>
<script>
const myApp = Vue.createApp({
    data() {
        return {
            form: {},
            errors: {}
        }
    },
    methods: {},
    mounted() {},
    watch: {}
})
window.app = myApp.mount('#root')
</script>
@endpush
```

---

## Template — Form Create / Edit

### Blade (HTML phần)

```blade
<fieldset class="border p-2 rounded">
    <legend class="float-none w-auto">Thông tin</legend>
    <div class="row g-3">

        <div class="col-md-4">
            <div class="form-group row align-items-center">
                <label class="col-sm-4 col-form-label text-nowrap">Tên <span class="text-danger">*</span></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control"
                        :class="{'is-invalid': errors.name}"
                        v-model="form.name">
                    <div v-if="errors.name" class="invalid-feedback">@{{ errors.name[0] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group row align-items-center">
                <label class="col-sm-4 col-form-label text-nowrap">Trạng thái</label>
                <div class="col-sm-8">
                    <select class="form-select"
                        :class="{'is-invalid': errors.status}"
                        v-model="form.status">
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Ngừng</option>
                    </select>
                    <div v-if="errors.status" class="invalid-feedback">@{{ errors.status[0] }}</div>
                </div>
            </div>
        </div>

    </div>
</fieldset>
```

### Vue script

```js
const myApp = Vue.createApp({
    data() {
        return {
            form: {
                name: '',
                status: 'active',
            },
            errors: {}
        }
    },
    methods: {
        save() {
            const self = this
            $.ajax({
                url: "{{ route('admin.xxx.store') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    ...this.form
                },
                success(response) {
                    showAlert(response.message, 'success', '#ajax-alert')
                    if (response.data?.url) {
                        setTimeout(() => window.location.href = response.data.url, 3000)
                    }
                },
                error(xhr) {
                    const response = xhr.responseJSON
                    if (response.errors) self.errors = response.errors
                    showAlert(response.message, 'danger', '#ajax-alert')
                }
            })
        }
    },
    mounted() {
        // Load dữ liệu nếu là trang Edit
        const record = @json($record ?? '')
        if (record !== '') {
            this.form = {
                name:   record.name,
                status: record.status,
            }
        }
    }
})
window.app = myApp.mount('#root')
```

---

## Template — Table có dòng động (thêm/xóa row)

Pattern dùng khi cần bảng detail (order_detail, invoice_item...).
Dòng trống cuối tự động thêm khi dòng trên đã có dữ liệu (watch details).

### Blade (HTML phần)

```blade
<fieldset class="border p-2 rounded mt-3">
    <legend class="float-none w-auto">Chi tiết</legend>
    <div class="table-responsive">
        <table class="table table-centered w-100">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in details" :key="item.row_id">
                    <td>
                        {{-- <select2> component tự dùng sau, bỏ qua ở đây --}}
                        <div v-if="errors['details.' + index + '.product_id']" class="invalid-feedback d-block">
                            @{{ errors['details.' + index + '.product_id'][0] }}
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control"
                            :class="{'is-invalid': errors['details.' + index + '.qty']}"
                            v-model="item.qty">
                        <div v-if="errors['details.' + index + '.qty']" class="invalid-feedback">
                            @{{ errors['details.' + index + '.qty'][0] }}
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control"
                            :class="{'is-invalid': errors['details.' + index + '.price']}"
                            v-model="item.price">
                        <div v-if="errors['details.' + index + '.price']" class="invalid-feedback">
                            @{{ errors['details.' + index + '.price'][0] }}
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-link text-danger"
                            v-if="item.product_id"
                            @click="deleteRow(index)">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</fieldset>
```

### Vue script

```js
const myApp = Vue.createApp({
    data() {
        return {
            form: { /* các field header */ },
            details: [],
            row_index: 0,
            errors: {}
        }
    },
    methods: {
        addRow(data = {}) {
            this.details.push({
                row_id:     this.row_index++,
                id:         data.id         || '',
                product_id: data.product_id || '',
                qty:        data.qty        || '',
                price:      data.price      || '',
            })
        },

        deleteRow(index) {
            this.details.splice(index, 1)
            // Dọn errors của dòng bị xóa
            const newErrors = { ...this.errors }
            Object.keys(newErrors).forEach(key => {
                if (key.startsWith(`details.${index}`)) delete newErrors[key]
            })
            this.errors = newErrors
        },

        save() {
            const self = this
            $.ajax({
                url: "{{ route('admin.xxx.store') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    ...this.form,
                    details: this.details,
                },
                success(response) {
                    showAlert(response.message, 'success', '#ajax-alert')
                    if (response.data?.url) {
                        setTimeout(() => window.location.href = response.data.url, 3000)
                    }
                },
                error(xhr) {
                    const response = xhr.responseJSON
                    if (response.errors) self.errors = response.errors
                    showAlert(response.message, 'danger', '#ajax-alert')
                }
            })
        }
    },

    watch: {
        details: {
            deep: true,
            handler(newVal) {
                // Tự thêm dòng trống khi dòng cuối đã có product
                const last = newVal[newVal.length - 1]
                if (last && last.product_id) {
                    this.addRow()
                }
            }
        }
    },

    mounted() {
        this.addRow() // dòng trống đầu tiên

        const record = @json($record ?? '')
        if (record !== '') {
            this.form = { /* map fields từ record */ }
            record.details.forEach(item => this.addRow(item))
        }
    }
})
window.app = myApp.mount('#root')
```

---

## Truyền dữ liệu từ Laravel vào Vue

```js
// Trong mounted() hoặc data(), dùng @json để Blade render thành JS
const record  = @json($record  ?? '')   // '' nếu Create, object nếu Edit
const options = @json($options ?? [])   // mảng dropdown

if (record !== '') {
    this.form = { ...record }
}
```

> **Không** dùng `'{{ $var }}'` cho object/array — sẽ bị escape sai.
> Chỉ dùng `@json()` cho data phức tạp; dùng `'{{ $scalar }}'` cho string/number đơn giản.

---

## AJAX — Thứ tự ưu tiên

**axios > $.ajax > fetch thuần**

- Có axios → dùng axios cho mọi AJAX call
- Không có axios nhưng có jQuery → dùng `$.ajax`
- jQuery chỉ dùng cho plugin (select2, datepicker...), không dùng `$.ajax` khi đã có axios

---

## AJAX với axios (ưu tiên)

Setup CSRF một lần trong `bootstrap.js` hoặc layout:

```js
window.axios.defaults.headers.common["X-CSRF-TOKEN"] = "{{ csrf_token() }}";
// hoặc nếu chưa setup global:
axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector(
    'meta[name="csrf-token"]',
)?.content;
```

Dùng trong Vue methods:

```js
// Form đơn giản
async save() {
    try {
        const response = await axios.post("{{ route('admin.xxx.store') }}", this.form)
        showAlert(response.data.message, 'success', '#ajax-alert')
        if (response.data.data?.url) {
            setTimeout(() => window.location.href = response.data.data.url, 3000)
        }
    } catch (error) {
        if (error.response?.data?.errors) this.errors = error.response.data.errors
        showAlert(error.response?.data?.message ?? 'Có lỗi xảy ra', 'danger', '#ajax-alert')
    }
},

// Có nested array (details) — axios tự serialize JSON đúng, không cần xử lý thêm
async save() {
    try {
        const response = await axios.post("{{ route('admin.xxx.store') }}", {
            ...this.form,
            details: this.details,
        })
        showAlert(response.data.message, 'success', '#ajax-alert')
        if (response.data.data?.url) {
            setTimeout(() => window.location.href = response.data.data.url, 3000)
        }
    } catch (error) {
        if (error.response?.data?.errors) this.errors = error.response.data.errors
        showAlert(error.response?.data?.message ?? 'Có lỗi xảy ra', 'danger', '#ajax-alert')
    }
},

// URL động cho Edit
const url = "{{ route('admin.xxx.edit', [':id']) }}".replace(':id', this.form.id)
const response = await axios.post(url, this.form)
```

---

## AJAX với $.ajax (fallback khi không có axios)

```js
// Form đơn giản
const self = this
$.ajax({
    url: "{{ route('admin.xxx.store') }}",
    method: "POST",
    data: {
        _token: '{{ csrf_token() }}',
        ...this.form
    },
    success(response) {
        showAlert(response.message, 'success', '#ajax-alert')
        if (response.data?.url) {
            setTimeout(() => window.location.href = response.data.url, 3000)
        }
    },
    error(xhr) {
        const response = xhr.responseJSON
        if (response?.errors) self.errors = response.errors
        showAlert(response?.message ?? 'Có lỗi xảy ra', 'danger', '#ajax-alert')
    }
})

// Có nested array — jQuery serialize mặc định không giữ đúng cấu trúc object
// Dùng JSON.stringify + contentType
$.ajax({
    url: "{{ route('admin.xxx.store') }}",
    method: "POST",
    contentType: 'application/json',
    data: JSON.stringify({
        _token: '{{ csrf_token() }}',
        ...this.form,
        details: this.details,
    }),
    success(response) { ... },
    error(xhr) { ... }
})
```

---

## Hiển thị lỗi validation từ Laravel (HTTP 422)

Laravel trả về `{ errors: { field: ['message'] } }`, Vue bind vào class và v-if:

```blade
{{-- Field đơn --}}
<input :class="{'is-invalid': errors.field_name}" v-model="form.field_name">
<div v-if="errors.field_name" class="invalid-feedback">@{{ errors.field_name[0] }}</div>

{{-- Field trong array (nested) --}}
<input :class="{'is-invalid': errors['details.' + index + '.qty']}" v-model="item.qty">
<div v-if="errors['details.' + index + '.qty']" class="invalid-feedback">
    @{{ errors['details.' + index + '.qty'][0] }}
</div>
```

Reset lỗi trước khi submit lại nếu cần:

```js
this.errors = {};
```

---

## Quy tắc

- Dùng `@{{ }}` cho Vue interpolation trong Blade — **không** dùng `{{ }}` (Blade sẽ xử lý mất)
- Truyền data từ Laravel vào Vue qua `@json()` trong `<script>`, không dùng `{{ }}` trong `data()`
- CSRF token với axios: set header `X-CSRF-TOKEN` global một lần; với `$.ajax`: truyền `_token` trong `data`
- `const self = this` khi dùng `$.ajax` và cần truy cập Vue instance trong callback; không cần khi dùng axios với async/await
- AJAX ưu tiên: **axios > $.ajax > fetch thuần**; jQuery chỉ dùng cho plugin
- Route URL trong script: `"{{ route(...) }}"` — Blade render thành URL thật trước khi JS chạy
- Vue chỉ mount 1 lần vào `#root` — tất cả state nằm trong app đó
- Không dùng `v-html` với data từ user (XSS risk)
- CDN Vue đặt trong layout chính nếu dùng nhiều trang
