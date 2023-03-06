<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    use HasFactory;

    protected $table = 'religion';

    public function teacher() {
        return $this->hasMany(Teacher::class, 'religion_id', 'id');
    }
}
