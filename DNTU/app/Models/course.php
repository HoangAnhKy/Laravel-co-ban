<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class course extends Model
{
    use HasFactory;
    public $fillable = ['name'];

    public function getCustomerDayAttribute(){
        return $this->created_at->format('d-m-Y');
    }
    public function student():hasMany
    {
        return $this->hasMany(student::class);
    }
}
