<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolInfo extends Model
{
    use HasFactory;

    protected $table = 'school_info';

    protected $fillable = [
        'school_name',
        'logo_url',
        'favicon_url',
        'email',
        'phone_no',
        'facebook_url',
        'linkedin_url',
        'about',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
