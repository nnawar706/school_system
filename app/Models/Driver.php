<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'driver';

    protected $fillable = [
        'name',
        'license_no',
        'phone_no',
        'nid_no',
        'photo_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function transport()
    {
        return $this->hasOne(Transportation::class, 'driver_id', 'id');
    }
}
