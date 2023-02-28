<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academic_year';

    protected $fillable = [
        'branch_id',
        'name'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $dates = ['deleted_at'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function academic_session_list()
    {
        return $this->hasMany(AcademicSession::class, 'academic_year_id', 'id');
    }
}
