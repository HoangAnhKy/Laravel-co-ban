<?php

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Students extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'gender',
        'birthdate',
        'status',
        'course_id',
    ];


    public function getGetGenAttribute()
    {
        return ($this->gender === 0) ? 'nam' : 'nữ';
    }

    public function getGetDayAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    public function getGetNameClassAttribute(){
        $courses = Course::get(['id', 'name']);
        return $courses->find($this->course_id)->name;
    }


    public function getGetAgeAttribute()
    {
        $now = new \DateTime();
        $data = new \DateTime($this->birthdate);
        return $now->diff($data)->y;
    }


    public function course():BelongsTo
    {
        return $this->BelongsTo(Course::class);
    }
}
