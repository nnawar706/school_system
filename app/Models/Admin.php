<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone_no',
        'nid_no',
        'religion_id',
        'dob',
        'gender_id',
        'profile_photo_url',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function religion() {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function gender() {
        return $this->belongsTo(Gender::class, 'gender_id');
    }
}
