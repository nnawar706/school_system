<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Branch extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'branch';

    protected $fillable = [
        'name',
        'location',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function user() {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }
}
