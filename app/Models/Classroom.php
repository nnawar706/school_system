<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classroom';

    protected $fillable = [
        'branch_id',
        'name',
        'max_student',
        'student_quantity',
        'active_status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
