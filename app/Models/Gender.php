<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasFactory;

    protected $table = 'gender';

    protected $fillable = [
        'name'
    ];

    public function admin() {
        return $this->hasMany(Admin::class, 'gender_id', 'id');
    }

    public function teacher() {
        return $this->hasMany(Teacher::class, 'gender_id', 'id');
    }
}
