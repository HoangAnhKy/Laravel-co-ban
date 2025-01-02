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

    // Đăng ký tự động thêm dữ liệu với các sự kiện
    protected static function booted()
    {
        if (auth()->check()){
            static::creating(function ($model) {
                $model->user_id = auth()->id();
            });
        }
    }

    /*
    Tên sự kiện	|                  Thời điểm xảy ra	                            | Áp dụng cho
    ------------|---------------------------------------------------------------|------------------------
    retrieved	| Khi model được truy vấn từ database (sau khi truy vấn).	    | Đọc dữ liệu
    creating	| Trước khi tạo một bản ghi mới.	                            | Tạo mới
    created	    | Sau khi tạo một bản ghi mới thành công.	                    | Tạo mới
    updating	| Trước khi cập nhật một bản ghi.	                            | Cập nhật
    updated	    | Sau khi cập nhật một bản ghi thành công.	                    | Cập nhật
    saving	    | Trước khi lưu (áp dụng cho cả tạo mới và cập nhật).	        | Lưu (tạo hoặc cập nhật)
    saved	    | Sau khi lưu thành công (áp dụng cho cả tạo mới và cập nhật).	| Lưu (tạo hoặc cập nhật)
    deleting	| Trước khi xóa một bản ghi.	                                | Xóa
    deleted	    | Sau khi xóa một bản ghi thành công.	                        | Xóa
    restoring	| Trước khi khôi phục một bản ghi đã bị xóa mềm.	            | Khôi phục
    restored	| Sau khi khôi phục thành công một bản ghi đã bị xóa mềm.	    | Khôi phục
    */
}

```