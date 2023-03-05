<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    use HasFactory;

    protected $table = 'transport_route';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function transportation()
    {
        return $this->hasMany(Transportation::class, 'transport_route_id', 'id');
    }
}
