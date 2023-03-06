<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subject';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    public function class_has_subject()
    {
        return $this->hasMany(ClassHasSubject::class, 'subject_id', 'id');
    }
}
