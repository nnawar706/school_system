<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassHasSubject extends Model
{
    use HasFactory;

    protected $table = 'class_has_subject';

    protected $fillable = [
        'class_id',
        'subject_id',
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    public function class() {
        return $this->belongsTo(Batch::class, 'class_id');
    }

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
