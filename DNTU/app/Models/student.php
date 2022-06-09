<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class student extends Model
{
    use HasFactory;
    public function getCustomerDayAttribute(){
        return $this->created_at->format('d-m-Y');
    }
    public function course():belongsTo {
        return $this->belongsTo(course::class);
    }
}
