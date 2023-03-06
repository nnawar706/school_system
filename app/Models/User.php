<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'role_id',
        'branch_id',
        'registration_id',
        'password',
        'salt_password',
        'last_password',
        'password_changed_at',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'last_password',
        'password_changed_at',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'remember_token',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function teacher() {
        return $this->hasMany(Teacher::class, 'user_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
