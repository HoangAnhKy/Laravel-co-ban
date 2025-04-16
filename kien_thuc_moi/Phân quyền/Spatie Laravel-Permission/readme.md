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
    php artisan migrate
    ```

# Thêm trait vào model User

Trong `app/Models/User.php`:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

# Tạo role và permission

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::create(['name' => 'admin']);
$permission = Permission::create(['name' => 'edit articles']);

$role->givePermissionTo($permission); // hoặc
$permission->assignRole($role);
```

#  Gán vai trò cho người dùng

```php
$user = User::find(1);
$user->assignRole('admin');
$user->assignRole(['create', 'editor']); // gián nhiều quyền
```

# Kiểm tra quyền

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