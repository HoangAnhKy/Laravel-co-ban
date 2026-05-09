---
name: react-ui
description: >
    Tạo React component với Inertia.js và Bootstrap 5. Chỉ dùng khi detect-stack
    xác nhận dự án dùng React + Inertia. Trigger khi user yêu cầu tạo page, form,
    table, component React trong dự án Laravel Inertia.
---

# react-ui

Tạo React + Inertia component chuẩn. Bootstrap 5, `useForm` của Inertia.

---

## Bước 0 — Xác định trước khi viết code

### 1. Tự phát hiện từ dự án

| Điều kiện                                    | Kết luận   |
| -------------------------------------------- | ---------- |
| File `.tsx` tồn tại trong `resources/js/`    | TypeScript |
| File `.jsx` hoặc `.js` trong `resources/js/` | JavaScript |
| `"typescript"` trong `package.json`          | TypeScript |

### 2. Nếu không phát hiện được → hỏi user

> "Dự án dùng React theo kiểu nào?
>
> **Ngôn ngữ:**
>
> 1. JavaScript (`.jsx`)
> 2. TypeScript (`.tsx`) ← khuyến nghị
>
> **Kiểu component:** 3. Function component + Hooks ← khuyến nghị 4. Class component
>
> (Gõ số hoặc kết hợp, ví dụ: 2 + 3)"

### 3. Mặc định nếu user không chọn

Dùng **TypeScript + Function component** — đây là chuẩn hiện tại của React.

---

## Cấu trúc thư mục

```
resources/js/
├── Pages/
│   └── Products/
│       ├── Index.tsx
│       ├── Create.tsx
│       └── Edit.tsx
├── Components/
│   └── Pagination.tsx
└── types/
    └── index.d.ts
```

---

## Định nghĩa types

```typescript
// resources/js/types/index.d.ts
export interface Product {
    id: number;
    name: string;
    price: string;
    status: "active" | "inactive";
    created_at: string;
}

export interface PaginatedData<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    meta: {
        current_page: number;
        last_page: number;
        total: number;
    };
}
```

---

## Template Index page

```tsx
// resources/js/Pages/Products/Index.tsx
import { useState } from "react";
import { Link, router } from "@inertiajs/react";
import { Product, PaginatedData } from "@/types";

interface Props {
    products: PaginatedData<Product>;
    filters: { search?: string; status?: string };
}

export default function Index({ products, filters }: Props) {
    const [deleting, setDeleting] = useState<Product | null>(null);

    function confirmDelete(): void {
        if (!deleting) return;
        router.delete(route("products.destroy", deleting.id), {
            onSuccess: () => setDeleting(null),
        });
    }

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-3">
                <h4 className="mb-0">Danh sách sản phẩm</h4>
                {/* Dùng Link của Inertia — không dùng <a href> để tránh full reload */}
                <Link
                    href={route("products.create")}
                    className="btn btn-primary"
                >
                    Thêm mới
                </Link>
            </div>

            <div className="card">
                <div className="card-body p-0">
                    <table className="table table-hover mb-0">
                        <thead className="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên</th>
                                <th>Giá</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {products.data.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan={4}
                                        className="text-center text-muted py-4"
                                    >
                                        Chưa có dữ liệu
                                    </td>
                                </tr>
                            ) : (
                                products.data.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.id}</td>
                                        <td>{item.name}</td>
                                        <td>{item.price}</td>
                                        <td className="text-end">
                                            <Link
                                                href={route(
                                                    "products.edit",
                                                    item.id,
                                                )}
                                                className="btn btn-sm btn-outline-primary me-1"
                                            >
                                                Sửa
                                            </Link>
                                            <button
                                                onClick={() =>
                                                    setDeleting(item)
                                                }
                                                className="btn btn-sm btn-outline-danger"
                                            >
                                                Xóa
                                            </button>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Pagination */}
            <div className="mt-3 d-flex justify-content-end">
                {products.links.map((link, i) => (
                    <Link
                        key={i}
                        href={link.url ?? "#"}
                        className={`btn btn-sm me-1 ${link.active ? "btn-primary" : "btn-outline-secondary"} ${!link.url ? "disabled" : ""}`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                ))}
            </div>

            {/* Modal xác nhận xóa */}
            {deleting && (
                <div
                    className="modal fade show d-block"
                    style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
                >
                    <div className="modal-dialog modal-sm">
                        <div className="modal-content">
                            <div className="modal-body text-center py-4">
                                <p className="mb-3">
                                    Bạn chắc chắn muốn xóa{" "}
                                    <strong>{deleting.name}</strong>?
                                </p>
                                <button
                                    onClick={confirmDelete}
                                    className="btn btn-danger me-2"
                                >
                                    Xóa
                                </button>
                                <button
                                    onClick={() => setDeleting(null)}
                                    className="btn btn-outline-secondary"
                                >
                                    Hủy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
```

---

## Template Form (Create/Edit dùng chung)

```tsx
// resources/js/Pages/Products/Create.tsx hoặc Edit.tsx
import { useForm } from "@inertiajs/react";
import { Link } from "@inertiajs/react";
import { Product } from "@/types";

interface Props {
    product?: Product;
}

export default function Form({ product }: Props) {
    const form = useForm({
        name: product?.name ?? "",
        price: product?.price ?? "",
        status: product?.status ?? ("active" as "active" | "inactive"),
    });

    function submit(e: React.FormEvent): void {
        e.preventDefault();
        product
            ? form.put(route("products.update", product.id))
            : form.post(route("products.store"));
    }

    return (
        <form onSubmit={submit}>
            <div className="mb-3">
                <label className="form-label">
                    Tên <span className="text-danger">*</span>
                </label>
                <input
                    type="text"
                    value={form.data.name}
                    onChange={(e) => form.setData("name", e.target.value)}
                    className={`form-control ${form.errors.name ? "is-invalid" : ""}`}
                />
                {form.errors.name && (
                    <div className="invalid-feedback">{form.errors.name}</div>
                )}
            </div>

            <div className="mb-3">
                <label className="form-label">
                    Giá <span className="text-danger">*</span>
                </label>
                <input
                    type="number"
                    value={form.data.price}
                    onChange={(e) => form.setData("price", e.target.value)}
                    className={`form-control ${form.errors.price ? "is-invalid" : ""}`}
                />
                {form.errors.price && (
                    <div className="invalid-feedback">{form.errors.price}</div>
                )}
            </div>

            <div className="mb-3">
                <label className="form-label">Trạng thái</label>
                <select
                    value={form.data.status}
                    onChange={(e) =>
                        form.setData(
                            "status",
                            e.target.value as "active" | "inactive",
                        )
                    }
                    className="form-select"
                >
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngừng</option>
                </select>
            </div>

            <div className="d-flex gap-2">
                <button
                    type="submit"
                    disabled={form.processing}
                    className="btn btn-primary"
                >
                    {form.processing ? "Đang lưu..." : "Lưu"}
                </button>
                <Link
                    href={route("products.index")}
                    className="btn btn-outline-secondary"
                >
                    Hủy
                </Link>
            </div>
        </form>
    );
}
```

---

## Variant: JavaScript (thay TypeScript)

Nếu user chọn JS, bỏ toàn bộ type annotation, đổi extension sang `.jsx`:

```jsx
// resources/js/Pages/Products/Index.jsx
import { useState } from 'react'
import { Link, router } from '@inertiajs/react'

export default function Index({ products, filters }) {
    const [deleting, setDeleting] = useState(null)

    function confirmDelete() {
        if (!deleting) return
        router.delete(route('products.destroy', deleting.id), {
            onSuccess: () => setDeleting(null),
        })
    }

    return (
        // ... JSX giống hệt TypeScript, chỉ bỏ type annotation
    )
}
```

> Không tạo file `types/index.d.ts` khi dùng JS.

---

## Variant: Class component (thay Function + Hooks)

Nếu user chọn Class component, áp dụng pattern sau:

**Index page:**

```tsx
// resources/js/Pages/Products/Index.tsx
import { Component } from "react";
import { Link, router } from "@inertiajs/react";
import { Product, PaginatedData } from "@/types";

interface Props {
    products: PaginatedData<Product>;
    filters: { search?: string; status?: string };
}

interface State {
    deleting: Product | null;
}

export default class Index extends Component<Props, State> {
    state: State = { deleting: null };

    confirmDelete = (): void => {
        const { deleting } = this.state;
        if (!deleting) return;
        router.delete(route("products.destroy", deleting.id), {
            onSuccess: () => this.setState({ deleting: null }),
        });
    };

    render() {
        const { products } = this.props;
        const { deleting } = this.state;

        return (
            <>
                <div className="d-flex justify-content-between align-items-center mb-3">
                    <h4 className="mb-0">Danh sách sản phẩm</h4>
                    <Link
                        href={route("products.create")}
                        className="btn btn-primary"
                    >
                        Thêm mới
                    </Link>
                </div>

                <div className="card">
                    <div className="card-body p-0">
                        <table className="table table-hover mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên</th>
                                    <th>Giá</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {products.data.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan={4}
                                            className="text-center text-muted py-4"
                                        >
                                            Chưa có dữ liệu
                                        </td>
                                    </tr>
                                ) : (
                                    products.data.map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.id}</td>
                                            <td>{item.name}</td>
                                            <td>{item.price}</td>
                                            <td className="text-end">
                                                <Link
                                                    href={route(
                                                        "products.edit",
                                                        item.id,
                                                    )}
                                                    className="btn btn-sm btn-outline-primary me-1"
                                                >
                                                    Sửa
                                                </Link>
                                                <button
                                                    onClick={() =>
                                                        this.setState({
                                                            deleting: item,
                                                        })
                                                    }
                                                    className="btn btn-sm btn-outline-danger"
                                                >
                                                    Xóa
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {deleting && (
                    <div
                        className="modal fade show d-block"
                        style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
                    >
                        <div className="modal-dialog modal-sm">
                            <div className="modal-content">
                                <div className="modal-body text-center py-4">
                                    <p className="mb-3">
                                        Bạn chắc chắn muốn xóa{" "}
                                        <strong>{deleting.name}</strong>?
                                    </p>
                                    <button
                                        onClick={this.confirmDelete}
                                        className="btn btn-danger me-2"
                                    >
                                        Xóa
                                    </button>
                                    <button
                                        onClick={() =>
                                            this.setState({ deleting: null })
                                        }
                                        className="btn btn-outline-secondary"
                                    >
                                        Hủy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </>
        );
    }
}
```

**Form (Create/Edit):**

```tsx
// resources/js/Pages/Products/Create.tsx
import { Component } from "react";
import { Link } from "@inertiajs/react";
import { useForm } from "@inertiajs/react";
import { Product } from "@/types";

// Class component không dùng được useForm() hook trực tiếp
// → wrap bằng HOC hoặc dùng router.post/put thủ công

interface Props {
    product?: Product;
}
interface State {
    name: string;
    price: string;
    status: "active" | "inactive";
    errors: Partial<Record<"name" | "price" | "status", string>>;
    processing: boolean;
}

export default class Form extends Component<Props, State> {
    state: State = {
        name: this.props.product?.name ?? "",
        price: this.props.product?.price ?? "",
        status: this.props.product?.status ?? "active",
        errors: {},
        processing: false,
    };

    submit = (e: React.FormEvent): void => {
        e.preventDefault();
        const { product } = this.props;
        const data = {
            name: this.state.name,
            price: this.state.price,
            status: this.state.status,
        };

        this.setState({ processing: true });

        product
            ? router.put(route("products.update", product.id), data, {
                  onError: (errors) =>
                      this.setState({ errors, processing: false }),
                  onSuccess: () => this.setState({ processing: false }),
              })
            : router.post(route("products.store"), data, {
                  onError: (errors) =>
                      this.setState({ errors, processing: false }),
                  onSuccess: () => this.setState({ processing: false }),
              });
    };

    render() {
        const { name, price, status, errors, processing } = this.state;

        return (
            <form onSubmit={this.submit}>
                <div className="mb-3">
                    <label className="form-label">
                        Tên <span className="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        value={name}
                        onChange={(e) =>
                            this.setState({ name: e.target.value })
                        }
                        className={`form-control ${errors.name ? "is-invalid" : ""}`}
                    />
                    {errors.name && (
                        <div className="invalid-feedback">{errors.name}</div>
                    )}
                </div>

                <div className="d-flex gap-2">
                    <button
                        type="submit"
                        disabled={processing}
                        className="btn btn-primary"
                    >
                        {processing ? "Đang lưu..." : "Lưu"}
                    </button>
                    <Link
                        href={route("products.index")}
                        className="btn btn-outline-secondary"
                    >
                        Hủy
                    </Link>
                </div>
            </form>
        );
    }
}
```

> ⚠️ Class component không dùng được `useForm()` hook — phải dùng `router.post/put` thủ công và tự quản lý `errors` + `processing` trong state.

---

Kiểm tra `package.json` có `"@reduxjs/toolkit"` trước. Nếu có, áp dụng pattern sau:

**Slice:**

```typescript
// resources/js/store/productSlice.ts
import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { Product } from "@/types";

interface ProductState {
    selected: Product | null;
    filters: { search: string; status: string };
}

const initialState: ProductState = {
    selected: null,
    filters: { search: "", status: "" },
};

export const productSlice = createSlice({
    name: "product",
    initialState,
    reducers: {
        setSelected: (state, action: PayloadAction<Product | null>) => {
            state.selected = action.payload;
        },
        setFilters: (
            state,
            action: PayloadAction<Partial<ProductState["filters"]>>,
        ) => {
            state.filters = { ...state.filters, ...action.payload };
        },
    },
});

export const { setSelected, setFilters } = productSlice.actions;
export default productSlice.reducer;
```

**Dùng trong component:**

```tsx
import { useDispatch, useSelector } from "react-redux";
import { RootState } from "@/store";
import { setSelected, setFilters } from "@/store/productSlice";

// Thay useState cho global state
const deleting = useSelector((state: RootState) => state.product.selected);
const dispatch = useDispatch();

// Thay setDeleting(item)
dispatch(setSelected(item));

// Thay setDeleting(null)
dispatch(setSelected(null));
```

> Phân biệt rõ: **Redux** cho state dùng chung nhiều component (filters, selected item, user session).
> **useState** cho state cục bộ 1 component (loading button, toggle UI).

---

## Quy tắc bắt buộc

- **Nếu dự án có Redux** (`@reduxjs/toolkit` trong `package.json`) → ưu tiên dùng Redux store cho global state, không dùng `useState` cho data dùng chung nhiều component
- State chỉ dùng riêng trong 1 component (ví dụ `deleting`, `loading`) → vẫn dùng `useState` bình thường

- Dùng `<Link>` của Inertia thay `<a href>` — tránh full page reload
- `useForm()` của Inertia — không dùng `axios` hay `fetch` trực tiếp
- TypeScript cho mọi component, định nghĩa `interface` rõ ràng, không dùng `any`
- Functional component + hooks
- Modal xóa render inline bằng state — không dùng `window.confirm()`
- Pagination render từ `products.links` — không tự viết logic phân trang
- Types tập trung trong `resources/js/types/index.d.ts`
