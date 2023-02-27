<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academic_session';

    protected $fillable = [
        'academic_year_id',
        'name'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
