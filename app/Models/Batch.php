<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $table = 'class';

    protected $fillable = [
        'branch_id',
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function class_has_subject()
    {
        return $this->hasMany(ClassHasSubject::class, 'class_id', 'id');
    }
}
