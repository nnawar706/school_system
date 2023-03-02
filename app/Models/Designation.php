<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'designation';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $dates = ['deleted_at'];
}
