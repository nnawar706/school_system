<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classroom';

    protected $fillable = [
        'branch_id',
        'name',
        'max_student',
        'student_quantity',
        'active_status'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
