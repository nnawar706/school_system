<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    use HasFactory;

    protected $table = 'transportation';

    protected $fillable = [
        'transport_route_id',
        'driver_id',
        'vehicle_reg_no',
        'pickup_time'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    public function transport_route() {
        return $this->belongsTo(TransportRoute::class, 'transport_route_id');
    }

    public function driver() {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
