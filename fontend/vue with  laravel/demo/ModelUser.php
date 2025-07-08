<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $appends = [
        'display_created',
    ];
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    function casts()
    {
        return [
            "password" => "hashed",
        ];
    }

    public function getDisplayCreatedAttribute()
    {
        return ($this->updated_at ?? $this->created_at)->format('d M Y H:i:s');
    }
}