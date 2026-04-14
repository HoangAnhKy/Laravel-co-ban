# lưu ý
khởi tạo route có name phân cấp rõ ràng ví dụ như `url: http://127.0.0.1:8000/admin/product/add`

nó sẽ có breadcrumd là Admin > Product > Add

# demo 

```php
public function createBreadcrumb(Request $request)
{
    $routeName = request()->route()->getName();
    if (!$routeName) return [];
    $segments = explode('.', $routeName);
    $breadcrumbs = [];
    $path = '';
    foreach ($segments as $index => $segment) {
        $path .= ($index === 0 ? '' : '.') . $segment;

        $breadcrumbs[] = [
            'name' => ucfirst($segment),
            'url' => (\Route::has($path) && $path !== $routeName)  ? route($path) : null
        ];
    }
    return $breadcrumbs;
}
```