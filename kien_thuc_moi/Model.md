# thao tác với modal

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // các cột được phép thay thế dữ liệu
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $guarded = []; // Không được phép thay thế

    protected $hidden = [
        'password',
        'remember_token',
    ];


    // Tự động sửa kiểu dữ liệu để phù hợp với database
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // tự động chuyển sang timestame
            'password' => 'hashed', // tự động mã hõa mật khẩu
        ];
    }
}

```