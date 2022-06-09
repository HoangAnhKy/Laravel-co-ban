<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use HasFactory;
    use Notifiable;
    public $timestamps = false;
    public $fillable  = [
        'name',
        'avatar',
        'level',
        'email',
        'password',
    ];
}
