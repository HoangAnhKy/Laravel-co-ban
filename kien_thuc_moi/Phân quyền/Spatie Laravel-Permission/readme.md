# Spatie Laravel-Permission

 Dùng để quản lý phân quyền theo vai trò (roles) và quyền hạn (permissions) cho người dùng trong database

 - `Gán vai trò (roles) cho người dùng`: ví dụ admin, editor, user, .v.v.

 - `Gán quyền hạn (permissions) cho người dùng hoặc vai trò`: ví dụ edit posts, delete users, view dashboard,...

# Cài package

- Cài đặt package

    ```sh
    composer require spatie/laravel-permission
    ```

- Cài File migration và config (nếu cần), file migration sẽ tạo các bảng roles, permissions, model_has_roles, role_has_permissions,...

    ```sh
    php artisan vendor:publish --tag="permission-migrations"
    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" # lấy cả config/permission
    php artisan migrate
    ```

# Lưu ý về các table thêm của permission 

## roles

`Mục đích`: Lưu trữ danh sách các vai trò trong hệ thống (ví dụ: admin, editor, user).

Các cột chính:

- `id`: Khóa chính.

- `name`: Tên vai trò.

- `guard_name`: Tên guard (ví dụ: web, admin).

- `created_at`, `updated_at`: Dấu thời gian tạo và cập nhật.

## permissions

`Mục đích`: Lưu trữ danh sách các quyền trong hệ thống (ví dụ: edit articles, delete users).

Các cột chính:

- `id`: Khóa chính.

- `name`: Tên quyền.

- `guard_name`: Tên guard.

- `created_at`, `updated_at`: Dấu thời gian tạo và cập nhật.

## model_has_roles  

`Mục đích`: Thiết lập mối quan hệ giữa các `model` (thường là người dùng) và `role`.

Các cột chính:

- `role_id`: ID của vai trò.

- `model_type`: Tên lớp của mô hình được gán vai trò (ví dụ: App\Models\User).

- `model_id`: ID của mô hình.​

Ví dụ: Khi gán vai trò admin cho người dùng có ID là 1, một bản ghi sẽ được thêm như sau

| key        | value             |
| ---------- | ----------------- |
| role_id    | 1                 |
| model_type | `App\Models\User` |
| model_id   | 1                 |

##  model_has_permissions

`Mục đích`: Thiết lập mối quan hệ giữa các mô hình và quyền, `cho phép gán quyền trực tiếp` mà không cần thông qua `role`.

Các cột chính:

- `permission_id`: ID của quyền.

- `model_type`: Tên lớp của mô hình được gán quyền.

- `model_id`: ID của mô hình.​

**`Lưu ý`**: Khi gán quyền cho người dùng thông qua `role`, quyền đó không được thêm vào bảng `model_has_permissions` mà được xác định thông qua bảng `role_has_permissions`.

## role_has_permissions

`Mục đích`: Thiết lập mối quan hệ giữa vai trò và quyền, xác định vai trò nào có những quyền nào.

Các cột chính:

- `permission_id`: ID của quyền.

- `role_id`: ID của vai trò.​

`Ví dụ:` Nếu vai trò `editor` có quyền `edit articles`, một bản ghi sẽ được thêm vào bảng này với `permission_id` và `role_id` tương ứng.


# Muốn đổi bảng khác theo cá nhân thì 

- Tạo model và kế  thừa `SpatieRole` của thư viện

    ```php
    namespace App\Models;

    use Spatie\Permission\Models\Role as SpatieRole;

    class CustomRole extends SpatieRole
    {
        protected $table = 'custom_roles'; // Tên bảng tùy chỉnh
    }
    ```

- Cấu hình `config/permission.php`

    ```php
    'models' => [
        'role' => App\Models\CustomRole::class,
        'permission' => App\Models\CustomPermission::class,
    ],
    ```

# Ứng dụng thư viện trong logic code

## Thêm trait vào model User

Trong `app/Models/User.php`:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

## Tạo role và permission

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::create(['name' => 'admin']);
$permission = Permission::create(['name' => 'edit articles']);

$role->givePermissionTo($permission); // hoặc
$permission->assignRole($role);
```

##  Gán vai trò cho người dùng

```php
$user = User::find(1);
$user->assignRole('admin');
$user->assignRole(['create', 'editor']); // gián nhiều quyền
```

## Kiểm tra quyền

### Trong controller hoặc logic php ở modal

```php
if ($user->hasRole('admin')) {
    // do something
}

if ($user->can('edit articles')) {
    // do something
}
```

### Ở giao diện blade

```php
@role('admin')
    <p>Chỉ admin thấy được nội dung này</p>
@endrole

@can('edit articles')
    <p>Chỉ ai có quyền edit articles mới thấy</p>
@endcan
```