<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    protected $table = 'academic_session';

    protected $fillable = [
        'academic_year_id',
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
