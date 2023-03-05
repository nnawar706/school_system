<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Role extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'role';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
