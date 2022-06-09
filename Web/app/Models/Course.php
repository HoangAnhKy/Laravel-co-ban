<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
    ];

    public function getGetDayAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }
    public function student():hasMany
    {
        return $this->hasMany(Students::class);
    }
}
