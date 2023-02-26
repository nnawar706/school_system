<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Role extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'role';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function user() {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
