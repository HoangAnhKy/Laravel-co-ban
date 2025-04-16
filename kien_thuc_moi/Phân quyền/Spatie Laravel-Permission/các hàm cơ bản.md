# kiểm tra quyền 

## hasRole

Kiểm tra user có vai trò nào đó không.

```php
if ($user->hasRole('editor')) {
    // Hiển thị giao diện chỉnh sửa bài viết
}
```

## hasAnyRole

Chỉ cần có 1 trong các quyền bên trong

```php
$user->hasAnyRole(['admin', 'editor']);
```

## hasAllRoles

Phải cần có đầy đủ quyền

```php
$user->hasAllRoles(['writer', 'reviewer']);
```

# hasPermissionTo

Kiểm tra user có quyền cụ thể (trực tiếp hoặc thông qua role)

```php
if ($user->hasPermissionTo('delete posts')) {
    // Hiện nút xóa bài viết
}
```

# can
Tương đương với hasPermissionTo, dùng được trong cả Blade và Controller.

```php
if ($user->can('publish posts')) {
    // Cho phép xuất bản
}

// fontend
@can('publish posts')
    <button>Xuất bản bài viết</button>
@endcan
```

# syncRoles / syncPermissions

Gỡ hết vai trò/quyền cũ rồi gán cái mới (đồng bộ hóa)

Hữu ích khi có form chọn vai trò hoặc quyền từ checkbox và muốn cập nhật lại toàn bộ.
```php
$user->syncRoles(['editor', 'reviewer']);
$role->syncPermissions(['edit posts', 'delete posts']);
```

# removeRole / revokePermissionTo

Xóa quyền

```php
$user->removeRole('editor');
$user->revokePermissionTo('delete posts');
```