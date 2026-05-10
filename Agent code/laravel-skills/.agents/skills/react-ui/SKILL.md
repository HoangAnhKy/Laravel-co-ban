---
name: react-ui
description: >
    Tạo UI cho dự án Laravel dùng React + Inertia.
    Trigger khi detect-stack xác nhận React hoặc React + Inertia,
    hoặc user yêu cầu tạo page, component, form dùng React.
---

## Hỏi trước khi làm

Nếu chưa rõ từ codebase, hỏi user 2 câu:

1. **JS hay TS?** — kiểm tra `tsconfig.json` hoặc file `.tsx` có tồn tại không
2. **Function hay Class component?** — kiểm tra các component hiện có trong `resources/js/`

> Mặc định đề xuất: **Function component + TS** (chuẩn React hiện đại)

## Cấu trúc thư mục

```
resources/js/
├── Pages/[Module]/Index.{tsx|jsx}, Create, Edit
├── Components/          ← shared components
├── Layouts/AppLayout.{tsx|jsx}
└── types/index.d.ts     ← chỉ khi dùng TS
```

## Khác biệt JS vs TS

|           | JS (.jsx)           | TS (.tsx)            |
| --------- | ------------------- | -------------------- |
| Props     | không khai báo type | `interface Props {}` |
| PageProps | không cần           | import từ `@/types`  |
| `any`     | tránh dùng          | cấm hoàn toàn        |

Types cần có khi dùng TS:

```ts
// types/index.d.ts
export interface PaginatedData<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
}
export interface PageProps {
    auth: { user: User };
    flash: { success?: string; error?: string };
}
```

## Khác biệt Function vs Class component

**Function (ưu tiên):**

```jsx
export default function Index({ products }) {
    const [search, setSearch] = useState("");
    // ...
}
```

**Class (chỉ dùng nếu codebase đang dùng):**

```jsx
export default class Index extends React.Component {
    state = { search: "" };
    render() {
        /* ... */
    }
}
```

## Patterns cốt lõi — Inertia

**Navigation & filter:**

```jsx
// Link nội bộ — không dùng <a>
<Link href={route("products.create")}>Thêm mới</Link>;

// Filter có preserveState — không reload cứng
router.get(
    route("products.index"),
    { search },
    { preserveState: true, replace: true },
);
```

**Form submit:**

```jsx
const { data, setData, post, put, processing, errors } = useForm({
    name: "",
    status: "active",
});
// submit: post(route('products.store')) hoặc put(route('products.update', id))
// errors.name tự lấy từ Laravel validation
```

**Pagination:**

```jsx
// dangerouslySetInnerHTML vì Laravel trả HTML entity trong label
{
    products.links.map((link) => (
        <Link
            key={link.label}
            href={link.url ?? "#"}
            className={`btn btn-sm ${link.active ? "btn-primary" : "btn-outline-secondary"} ${!link.url ? "disabled" : ""}`}
            dangerouslySetInnerHTML={{ __html: link.label }}
        />
    ));
}
```

**Flash message:**

```jsx
const { flash } = usePage().props; // usePage<PageProps>() nếu TS
{
    flash.success && <div className="alert alert-success">{flash.success}</div>;
}
```

## Controller — Inertia::render

```php
return Inertia::render('Products/Index', [
    'products' => ProductResource::collection($this->service->getAll($request->only(['search']))),
    'filters'  => $request->only(['search']),
]);
```

## Quy tắc cứng

- `<Link>` từ Inertia cho internal navigation, không dùng `<a>`
- `useForm()` cho mọi form — không fetch/axios thủ công
- `preserveState: true, replace: true` cho filter/search
- Class component: chỉ dùng nếu codebase đang có — không tự ý đổi sang function
- TS: không dùng `any`, khai báo `interface` cho mọi props
- Pagination dùng `products.links` từ Laravel paginator
